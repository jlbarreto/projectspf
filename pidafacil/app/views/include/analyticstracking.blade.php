<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-66346308-1', 'auto');
    ga('send', 'pageview');

</script>

<link rel="stylesheet" href="https://js.appboycdn.com/web-sdk/1.2/appboy.min.css" />
<script type="text/javascript">
  +function(a,p,P,b,y) {
    appboy={};for(var s="destroy toggleAppboyLogging setLogger openSession changeUser requestImmediateDataFlush requestFeedRefresh subscribeToFeedUpdates logCardImpressions logCardClick logFeedDisplayed requestInAppMessageRefresh logInAppMessageImpression logInAppMessageClick logInAppMessageButtonClick subscribeToNewInAppMessages removeSubscription removeAllSubscriptions logCustomEvent logPurchase isAppboyPushSupported registerAppboyPushMessages unregisterAppboyPushMessages ab ab.User ab.User.Genders ab.User.NotificationSubscriptionTypes ab.User.prototype.getUserId ab.User.prototype.setFirstName ab.User.prototype.setLastName ab.User.prototype.setEmail ab.User.prototype.setGender ab.User.prototype.setDateOfBirth ab.User.prototype.setCountry ab.User.prototype.setHomeCity ab.User.prototype.setEmailNotificationSubscriptionType ab.User.prototype.setPushNotificationSubscriptionType ab.User.prototype.setPhoneNumber ab.User.prototype.setAvatarImageUrl ab.User.prototype.setLastKnownLocation ab.User.prototype.setUserAttribute ab.User.prototype.setCustomUserAttribute ab.User.prototype.addToCustomAttributeArray ab.User.prototype.removeFromCustomAttributeArray ab.User.prototype.incrementCustomUserAttribute ab.InAppMessage ab.InAppMessage.SlideFrom ab.InAppMessage.ClickAction ab.InAppMessage.DismissType ab.InAppMessage.prototype.subscribeToClickedEvent ab.InAppMessage.prototype.subscribeToDismissedEvent ab.InAppMessage.prototype.removeSubscription ab.InAppMessage.prototype.removeAllSubscriptions ab.InAppMessage.Button ab.InAppMessage.Button.prototype.subscribeToClickedEvent ab.InAppMessage.Button.prototype.removeSubscription ab.InAppMessage.Button.prototype.removeAllSubscriptions ab.SlideUpMessage ab.ModalMessage ab.FullScreenMessage ab.Feed ab.Feed.prototype.getUnreadCardCount ab.Card ab.ClassicCard ab.CaptionedImage ab.Banner display display.automaticallyShowNewInAppMessages display.showInAppMessage display.showFeed display.destroyFeed".split(" "),i=0;i<s.length;i++){for(var k=appboy,l=s[i].split("."),j=0;j<l.length-1;j++)k=k[l[j]];k[l[j]]=function(){}}appboy.initialize=function(){console&&console.log("Appboy cannot be loaded - this is usually due to strict corporate firewalls or ad blockers.")};appboy.getUser=function(){return new appboy.ab.User};appboy.getCachedFeed=function(){return new appboy.ab.Feed};
    (y = a.createElement(p)).type = 'text/javascript';
    y.src = 'https://js.appboycdn.com/web-sdk/1.2/appboy.min.js';
    (c = a.getElementsByTagName(p)[0]).parentNode.insertBefore(y, c);
    if(y.addEventListener){
      y.addEventListener("load", b, false);
    }else if (y.readyState){
      y.onreadystatechange = b;
    }
  }(document, 'script', 'link', function() {
    appboy.initialize('d7e4222c-84dd-4f2a-b9eb-1af1998ceb40');
    appboy.display.automaticallyShowNewInAppMessages();
    appboy.openSession();
    appboy.registerAppboyPushMessages();
    //appboy.isPushSupported();
    /*
     * By default, in-app messages are refreshed every 10 minutes as part of the openSession call. If you want them
     * refreshed as part of every page load instead, uncomment this line - note that depending on your website's
     * architecture and messaging strategy, this may cause users to receive messages very frequently as they navigate
     * around the site, and therefore may not be desirable.
     */
    // appboy.requestInAppMessageRefresh();

    @if(Session::has('email'))
      appboy.changeUser("{{ Session::get('email') }}");
    @endif
  });
</script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.8&appId=281930302168618";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>