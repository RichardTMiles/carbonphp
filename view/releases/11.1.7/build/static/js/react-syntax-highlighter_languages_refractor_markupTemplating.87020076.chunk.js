(this["webpackJsonpcarbonphp-documentation-react"]=this["webpackJsonpcarbonphp-documentation-react"]||[]).push([[81],{793:function(e,n,t){"use strict";function a(e){!function(e){function n(e,n){return"___"+e.toUpperCase()+n+"___"}Object.defineProperties(e.languages["markup-templating"]={},{buildPlaceholders:{value:function(t,a,o,r){if(t.language===a){var c=t.tokenStack=[];t.code=t.code.replace(o,(function(e){if("function"===typeof r&&!r(e))return e;for(var o,i=c.length;-1!==t.code.indexOf(o=n(a,i));)++i;return c[i]=e,o})),t.grammar=e.languages.markup}}},tokenizePlaceholders:{value:function(t,a){if(t.language===a&&t.tokenStack){t.grammar=e.languages[a];var o=0,r=Object.keys(t.tokenStack);!function c(i){for(var p=0;p<i.length&&!(o>=r.length);p++){var u=i[p];if("string"===typeof u||u.content&&"string"===typeof u.content){var s=r[o],g=t.tokenStack[s],l="string"===typeof u?u:u.content,f=n(a,s),k=l.indexOf(f);if(k>-1){++o;var h=l.substring(0,k),m=new e.Token(a,e.tokenize(g,t.grammar),"language-"+a,g),d=l.substring(k+f.length),y=[];h&&y.push.apply(y,c([h])),y.push(m),d&&y.push.apply(y,c([d])),"string"===typeof u?i.splice.apply(i,[p,1].concat(y)):u.content=y}}else u.content&&c(u.content)}return i}(t.tokens)}}}})}(e)}e.exports=a,a.displayName="markupTemplating",a.aliases=[]}}]);
//# sourceMappingURL=react-syntax-highlighter_languages_refractor_markupTemplating.87020076.chunk.js.map