<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-66346308-1', 'auto');
    ga('send', 'pageview');

</script>

<link rel="stylesheet" href="https://js.appboycdn.com/web-sdk/0.2/appboy.min.css" />
<script type="text/javascript">
  +function(a,p,P,b,y) {
    (y = a.createElement(p)).type = 'text/javascript';
    y.src = 'https://js.appboycdn.com/web-sdk/0.2/appboy.min.js';
    (c = a.getElementsByTagName(p)[0]).parentNode.insertBefore(y, c);
    if (y.addEventListener) {
      y.addEventListener("load", b, false);
    }
    else if (y.readyState) {
      y.onreadystatechange = b;
    }
  }(document, 'script', 'link', function() {
    // appboy may be null on very old unsupported browsers
    if (typeof(appboy) !== 'undefined') {
      appboy.initialize('d7e4222c-84dd-4f2a-b9eb-1af1998ceb40');
      appboy.toggleAppboyLogging()
      appboy.display.automaticallyShowNewInAppMessages();
      appboy.openSession();
      
    }

    @if(Session::has('email'))
        appboy.changeUser("{{ Session::get('email') }}");
    @endif
  });
</script>