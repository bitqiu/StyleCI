@if(Config::get('analytics.googleanalytics'))
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', '{{ Config::get('analytics.googleanalytics') }}');
ga('send', 'pageview');
</script>
@endif
@if(Config::get('analytics.gosquared'))
<script>
!function(g,s,q,r,d){r=g[r]=g[r]||function(){(r.q=r.q||[]).push(
arguments)};d=s.createElement(q);q=s.getElementsByTagName(q)[0];
d.src='//d1l6p2sc9645hc.cloudfront.net/tracker.js';q.parentNode.
insertBefore(d,q)}(window,document,'script','_gs');
_gs('{{ Config::get('analytics.gosquared') }}');
</script>
@endif
