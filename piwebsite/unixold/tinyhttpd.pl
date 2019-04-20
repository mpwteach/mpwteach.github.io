#!/usr/bin/perl
# TinyHTTPD - a minimum-functional HTTP server written in -*- Perl -*-
# -ot.0894
# $Id: httpd.pl,v 1.4 1994/08/15 17:22:28 fsinf01 Exp $

# Currently supported: HTTP 1.0 GET and POST queries
# File types of .html and .gif
# Limited subset of CGI: response is not parsed
# Script URLs must begin with /cgi-bin.

$ENV{'SERVER_SOFTWARE'}="TinyHTTPD $Revision: 1.4 $ -ot.0894";

## Configuration section
$port=8080;                     # Port on which we listen
$htmldir="\./public_html";      # Base directory for HTML files
$cgidir="\./cgi-bin";            # Base dir. for CGI
# Acess control
%acl=
    # "host-pattern", "url-pattern". Prefix ! to url means deny
    (
     ".*",                       "."
     );
## End configuration section

# the following substitutes "require 'sys/socket.ph';" on ultrix
# Check if the definitions are correct with /usr/include/sys/socket.h
$AF_INET=2; $PF_INET=$AF_INET; $SOCK_STREAM=1;

# Messages
%errors=
    (
     "403", "Forbidden",
     "404", "Not Found",
     "500", "Internal Error",
     "501", "Not Implemented",
     );
%verrors=
    (
     "403", "Your client is not allowed to request this item",
     "404", "The requested item was not found on this server",
     "500", "An error occurred while trying to retrieve item",
     "501", "This server does not support the given request type",
     );

(($>)&&($<==$>)&&($(==$))) || die "Don't run this program with privileges!\n";

# set up a server socket, redirect stderr to logfile
$IPPROTO_TCP=6;
$sockaddr = 'S n a4 x8';
$this = pack($sockaddr, $AF_INET, $port, "\0\0\0\0");
socket(S, $PF_INET, $SOCK_STREAM, $IPPROTO_TCP) || die "socket: $!";
bind(S, $this) || die "bind: $!";
listen(S, 5) || die "listen: $!";
open(LOG,">> httpd.log");
select(LOG); $|=1;
open(STDERR, ">&LOG") || die "dup2 log->stderr";

# accept incoming calls
for (;;) {
    ($addr=accept(NS,S)) || die "accept: $!";
    ($a,$p,$inetaddr) = unpack($sockaddr, $addr);
    # print LOG "** DEBUG1 $inetaddr\n";
    @inetaddr = unpack('C4', $inetaddr);
    # print LOG "** DEBUG2 $inetaddr\n";
    ($host,$aliases) = gethostbyaddr($inetaddr, $AF_INET);
    $inetaddr = join(".", @inetaddr);
    # print LOG "** DEBUG3 $inetaddr\n";
    @host=split(' ', "$host $aliases");
    $host || do { $host = $inetaddr; };
    @t=localtime;
    print LOG "** @t[1..5]: $$ connect from $host ($inetaddr)\n";
    open(STDIN, "+<&NS") || die "dup2 ns->stdin";
    open(STDOUT, "+>&NS") || die "dup2 ns->stdout";
    select(STDOUT); $|=1;
    &serve_request;
    close(STDIN); close(STDOUT);
}

# Read request from stdin and produce output
sub serve_request {

    # Analyze HTTP input.
    $_=<STDIN>;
    print LOG $_,"\n";
    ($method, $url, $proto) = split;
    if ($proto) {
        while (<STDIN>) {
            print LOG $_,"\n";
            s/\n|\r//g; # kill CR and NL chars
            /^Content-Length: (\S*)/i && ($content_length=$1);
            /^Content-Type: (\S*)/i && ($content_type=$1);
            length || last; # empty line - end of header
        }
    } else {
        $proto="HTTP/0.9";
    }
    ($method=~/^(GET|POST)$/) || do { &error(501,$method); return; };
    ($proto eq "HTTP/1.0") || do { &error(501,$proto); return; };

    $url=~s:/$:/index.html:;    # URL ending in / gets substituted
    print LOG "$$ Request: $method $url\n";
    # prevent directory go-back
    $url=~/\.\./ && do { &error(403,$url,"contains go-back"); return; };

    # Check access control
    $allow=0;
    foreach $k (keys %acl) {
        foreach $host (@host) {
            if ($host=~/$k/i) {
                $acurl=$acl{$k};
                $deny=($acurl=~s/^!//);
                if ($url=~/$acurl/) {
                    if ($deny) {
                        &error(403,$url,"on deny list: $acurl $host");
                        return; }
                    else { $allow=1; };
                }
            }
        }
    }
#   $allow || do { &error(403,$url,"not on allow list"); return; };

    if ($url=~m:^/cgi-bin/:) {

# Execute CGI scripts
        # TODO: SERVER_NAME, PATH_INFO, PATH_TRANSLATED,
        # SCRIPT_NAME, QUERY_STRING
        $ENV{'CONTENT_TYPE'}=$content_type;
        $ENV{'CONTENT_LENGTH'}=$content_length;
        $ENV{'GATEWAY_INTERFACE'}="CGI/1.0";
        $ENV{'SERVER_PORT'}=$port;
        $ENV{'SERVER_PROTO'}=$proto;
        $ENV{'REQUEST_METHOD'}=$method;
        $ENV{'REMOTE_ADDR'}=$inetaddr;
        # generate command line
        $file=$url;
        $file=~s:^/cgi-bin:$cgidir:o;
        $file=~s/\?.*$//;
        $srch=$url;
        $srch=~s/^[^\?]*\??//;
        $srch="" if (length($srch)>250); # safe side
        (-x $file) || do { &error(404,$url); return; };
        print LOG "$$ Executing: $url ($srch)\n";
        # Parsing of sent-back headers is not implemented.
        # Script effectively talks back to client.
        print "HTTP/1.0 200 OK\nMIME-Version: 1.0\n";
        open(PIP, "|$file $srch") || do {
            &error(500,$url,"pipe: $!"); return; };
        select(PIP); $|=1;
        while (1) {
            # don't read past content-length - client doesn't give EOF
            $len=length;
            s/\r//g;
            print LOG;
            print PIP;
            if (($content_length-=$len)<=0) { last; };
            $_=<STDIN>;
        }
        print LOG "$$ **\n";
        close(PIP);

    } else {

# Get and return file

        $file="$htmldir$url";
        (-r "$file") || do { &error(404,$url); return; };
        # output the file
        print "HTTP/1.0 200 OK\nMIME-Version: 1.0\nContent-Type: ";
        CASE:
        {
            $url=~/\.html$/ && do { print "text/html\n\n"; last CASE; };
            $url=~/\.gif$/ && do { print "image/gif\n\n"; last CASE; };
            print "text/plain\n\n";
        }
        system("cat $file");
    }
}

sub error {
    # generate error response
    local($errno) = @_[0];
    local($errmsg) = "$errno $errors{$errno}";
    print LOG "$$ $errmsg (@_[1,2])\n";
    print <<TheEnd;
HTTP/1.0 $errmsg
MIME-version: 1.0
Content-type: text/html

<HTML>
<HEAD><TITLE>$errmsg</TITLE></HEAD>
<BODY><H1>$errmsg</H1>
$verrors{$errno}: <PRE> @_[1] </PRE>
<HR>
<ADDRESS><A HREF="http://rzstud1.rz.uni-karlsruhe.de/~uknf/sw/tinyhttpd.html">
$ENV{'SERVER_SOFTWARE'}</A></ADDRESS>
</BODY>
</HTML>
TheEnd
}

