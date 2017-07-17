/*Written by nhoward for OCLS
This script allows a template loading from the Summon 2.0 interface to be injected with a custom elements. It replaces the z3988 field
which isn't used with a custom div.
All external calls are made to the http://serveraddress where we will process and return needed data
*/

//Below we allow resources to be loaded from our serveraddress. 
angular.module('summonApp').config(function($sceDelegateProvider) {  

$sceDelegateProvider.resourceUrlWhitelist([
    // Allow same origin resource loads.
    'self',
    // Allow loading from our assets domain. **.
    'http://serveraddress/**'
  ]);

});

//Here we replace the documentSummary.html z3988 with our own div
angular.module('summonApp').run([ '$templateCache', function (templateCache) {
    var docSummary = "/assets/documentSummary.html";
    var v = templateCache.get(docSummary);
    v = v.replace(/(<span.*Z3988.*<\/span>)/,
                          
                          "<div ng-controller='LicenseController'>"+
                          "<a class={{toggle}} tabindex='0' ng-click='getTerms()' role='button' aria-expanded={{pressed}}></br>{{ license }}</a>"+
                          "&nbsp&nbsp&nbsp&nbsp<img src='{{myImg}}' tabindex='0' alt='Question mark' height='15' width='15' role='tooltip' uib-tooltip ='Centennial College has negotiated license agreements for use of electronic resources on behalf of its users.These license agreements are formal contracts.' tooltip-placement='bottom'>"+
                          "<div ng-show='viewit' style='background-color: #F0F0F0;width: 50%;'></div>"+
                          "<div ng-show = 'state'><img ng-src='{{imageSource}}' height='13' alt='loading'></div>"+
                          "<div ng-show = 'state' aria-hidden={{hidden}}>"+
                          "<table role='presentation'>"+
                          "<ul role='list' style='list-style: none; padding-left:0;' ng-repeat='x in records '>"+
                          "<ul role='list' style='list-style: none; padding-left:0;' ng-repeat='n in x | limitTo:quantity'>"+
                          "<h4>{{n['license-name']}}</h4>"+
                          "<p><a ng-href={{url1}}{{n['license-tag']}} target='_blank'>{{check}}</a></p>"+
                          "<li role='listitem'><span ng-style='calculateStyle(n.e_reserves.usage)'>{{n.e_reserves.usage}}&nbsp</span>&nbsp{{n.e_reserves.case}}&nbsp<a aria-hidden='true' ng-href='mailto:libary@centennialcollege.ca?body=%0D%0A%0D%0AHOURS%0D%0ACONTACT' aria-label='Library Email'>{{ask_lib}}</a></li>"+
                          "<li role='listitem'><span ng-style='calculateStyle(n.cms.usage)'>{{n.cms.usage}}&nbsp</span>&nbsp{{n.cms.case}}&nbsp<a aria-hidden='true' ng-href='mailto:libary@centennialcollege.ca?body=%0D%0A%0D%0AHOURS%0D%0ACONTACT' aria-label='Library Email'>{{ask_lib}}</a></li>"+
                          "<li role='listitem'><span ng-style='calculateStyle(n.course_pack.usage)'>{{n.course_pack.usage}}&nbsp</span>&nbsp{{n.course_pack.case}}&nbsp<a aria-hidden='true' ng-href='mailto:libary@centennialcollege.ca?body=%0D%0A%0D%0AHOURS%0D%0ACONTACT' aria-label='Library Email'>{{ask_lib}}</a></li>"+
                          "<li role='listitem'><span ng-style='calculateStyle(n.durable_url.usage)'>{{n.durable_url.usage}}&nbsp</span>&nbsp{{n.durable_url.case}}&nbsp<a aria-hidden='true' ng-href='mailto:libary@centennialcollege.ca?body=%0D%0A%0D%0AHOURS%0D%0ACONTACT' aria-label='Library Email'>{{ask_lib}}</a></li>"+
                          "<li role='listitem'><span ng-style='calculateStyle(n.print.usage)'>{{n.print.usage}}&nbsp</span>&nbsp{{n.print.case}}&nbsp<a aria-hidden='true' ng-href='mailto:libary@centennialcollege.ca?body=HELLO%0D%0ATEST' aria-label='Library Email'>{{ask_lib}}</a></li><br><br>"+
                          "</ul></ul></td>"+
                          "</tr>"+
                          "<tr><td><a class={{styleThis}} tabindex='0' ng-click='getMore()' role='button' aria-expanded={{press2}} aria-label='Show More Options' style='color:black;margin-bottom:3px;border-color:black;'>{{shoMore}}</a></td>&nbsp&nbsp&nbsp&nbsp<td><a class={{styleLess}} tabindex='0' ng-click='getLess()' role='button' aria-expanded={{press3}} aria-label='Show Less Options' style='color:black;margin-bottom:3px;border-color:black;'>{{shoLess}}</a></td></tr>"+
                          "<tr><td><span ng-show = 'no_show'><p ng-style='color_ask(no_ask)'>{{no_ask}}</p><p><b>{{who_do}}</b><a aria-hidden={{mail}} href='mailto:libary@centennialcollege.ca?body=HELLO%0D%0ATEST?body=HELLO%0D%0ATEST' aria-label='Library Email'>&nbsp{{no_res}}</a></p></span></td></tr>"+
                          "</table>"+
                          "</div>"+
                          "</div>");

    templateCache.put(docSummary, v);

}]);

//Here we attach the controller to the $scope and make our call to our PHP file for results. We then style the elements in our controller
angular.module('summonApp.directives')
.controller('LicenseController', ['$scope','$http',function($scope,$http,$compile) {
    $scope.press2 = "false";
    $scope.press3 = "false";
    $scope.hidden = "true";
    $scope.hideImg = "false";
    
    $scope.myImg = "http://server/quest.png";
    /*$scope.license = "Permitted Uses";*/
    $scope.toggle = "togglePreview customColorsToggleButton ng-scope";
    $scope.license = "Permitted Uses";
    $scope.imageSource = "http://serveraddress/spinner.gif";
    $( "span" ).each(function( index ) {
      
        if($( this ).text() == "Book"){
            
            $(this).next().next().next().hide();
        }
    });
 
    $scope.styleThis = "";

    //ON CLICK SEND OUR SOURCE NAME TO SERVER SIDE SCRIPT
    $scope.getTerms = function() {
        
        $scope.no_show = !$scope.no_show;
        $scope.pressed = !$scope.pressed;
        /*$scope.pressed = "true";*/
        $scope.hidden = "false";
        var sources = [];
        var source = "";
     
        if($scope.toggle == "togglePreview customColorsToggleButton ng-scope"){
          $scope.toggle = "togglePreview customColorsToggleButton ng-scope active";
        }
       else{
          $scope.toggle = "togglePreview customColorsToggleButton ng-scope";
        }
      
        $scope.state = !$scope.state;
        
        
        if($scope.document.issns && $scope.document.issns.length >= 1){
           source = "ISSN";
           for(var i = 0; i < $scope.document.issns.length; i++){
            
              name = encodeURIComponent($scope.document.issns[i]);
              sources[i] = name;
            
          }
        }
        if($scope.document.eissns && $scope.document.eissns.length >= 1){
          source = "EISSN";
           for(var x = 0; x < $scope.document.eissns.length; x++){
            
              name = encodeURIComponent($scope.document.eissns[x]);
              sources[x] = name;
            
          }
        }
        if($scope.document.eisbns && $scope.document.eisbns.length >= 1){
          source = "EISBNS";
           for(var x = 0; x < $scope.document.eisbns.length; x++){
            
              name = encodeURIComponent($scope.document.eisbns[x]);
              sources[x] = name;
            
          }
        }
        if($scope.document.isbns && $scope.document.isbns.length >= 1){
          source = "ISBNS";
           for(var x = 0; x < $scope.document.isbns.length; x++){
            
              name = encodeURIComponent($scope.document.isbns[x]);
              sources[x] = name;
            
          }
        }
        //LOCATIONS OF VIDEO INFO FOUND IN CORPORATE AUTHORS OR SUBJECT TERMS, MORE MAY HAVE TO BE
        if($scope.document.corporate_authors && $scope.document.corporate_authors.length >= 1 && !$scope.document.lc_call_numbers){

           source = "VIDEO";
           for(var x = 0; x < $scope.document.corporate_authors.length; x++){
            
            name = encodeURIComponent($scope.document.corporate_authors[x].fullname);
            sources[x] = name;
            
          }
         
          
        }
        //SHOW ASK BECAUSE IT HAS A CALL NUMBER FOR A VIDEO
        /*if($scope.document.corporate_authors && $scope.document.corporate_authors.length >= 1 && $scope.document.lc_call_numbers){
          console.log("SHOW ASK BECAUSE IT HAS A CALL NUMBER FOR A VIDEO");
            $scope.color_ask = function(ask) {
                style ={}
                style.color='#000000';
                style.fontWeight='bold';
                style.background = '#ff9900';
                style.width = '30px';
               
                style.padding = '1px 1px 1px 3px';
                
                style.fontWeight='bold'; 
                return style;
            }
            
            
              
             
            $scope.no_ask = "Ask";
            $scope.who_do = "Who do I ask?"
            $scope.no_res = "Ask the library.";
           
         
          
        }*/
        if($scope.document.corporate_authors.length == 0 && sources.length == 0 && !$scope.document.lc_call_numbers){
            
            if($scope.document.subject_terms && $scope.document.subject_terms.length >= 1 ){

                 source = "VIDEO";
                 for(var x = 0; x < $scope.document.subject_terms.length; x++){
                  
                  name = encodeURIComponent($scope.document.subject_terms[x]);
                  sources[x] = name;
                  
                }
 
            }
        }
        var college = ""; 
        if(window.location.href.indexOf("centennial") > -1) {
            college = "centennial";
        }
        
        for(var j = 0; j < sources.length; j++){

            url = "http://serveraddress/listener.php?college=" + college + "&license=" + sources + "&source=" + source;
            console.log(url);
            $http.jsonp(url,{jsonpCallbackParam: 'callback'})
                .then(function(data){
                  $scope.mail = "true";
                  var count = 0;
                  $scope.imageSource = "http://serveraddress/white.PNG";
                  var array = [];
                  
                  console.log(data);
                  if(data !== null){
                    array = $.map(data.data, function(value, index) {
                       
                        return value;
                    });
                  }
   
                  if(data === null || data[Object.keys(data)[0]] == null){

                    $scope.records = [data];
                  }else{
                    $scope.records = [array];
                  }
                  /*$scope.records = [array];*/
                 
                  console.log($scope.records[0]);
                  
                  
                  angular.forEach(data, function(value, key){
                       
                       angular.forEach(value, function(val,k){
                            
                            if(val.usage == 'Ask'){
                              count++;
                            }
                       });
                      
                  });

                  if(data === null || data[Object.keys(data)[0]] == null || $scope.records[0].length == 0){
                      $scope.records = null;
                      $scope.mail = "false";
                      $scope.no_ask = "Ask";
                      $scope.who_do = "Who do I ask?"
                      $scope.no_res = "Ask the library.";
                     
                      $scope.no_show = function() {
                         style ={}
                         style.width = '12px';
                      }
                      $scope.color_ask = function(ask) {
                          style ={}
                          style.color='#000000';
                          style.fontWeight='bold';
                          style.background = '#ff9900';
                          style.width = '30px';
                          style.padding = '1px 1px 1px 3px';
                          style.fontWeight='bold'; 
                          return style;
                      }
                    
                  }else{
                      $scope.hides = "true";
                      $scope.check = "More Info";
                  }
                 
                  $scope.calculateStyle = function(test) {
                      var style={}
                      if (test == 'Yes') {
                        $scope.ask_lib = '';
                        $scope.link = '';
                        style.padding = '1px 1px 1px 5px';
                        style.background = '#33bb55';
                        style.fontWeight='bold';  
                       
                      }
                      if(test == 'Ask'){
                        $scope.ask_lib = 'Ask the library';
                        style.padding = '1px 1px 1px 3px';
                        style.background = '#ff9900';
                        style.fontWeight='bold'; 
                        count + 1;
                       
                        
                      }
                      if(test == 'No'){
                        $scope.ask_lib = '';
                        $scope.link = '';
                        style.fontWeight='bold';
                        style.padding = '1px 4px 1px 7px';
                        style.background = '#ff3333';    
                        
                      }  
                      return style;
                  }
                  $scope.e_res = {
                          "color" : "#33bb37",
                          "font-weight": "bold"
                      }
                  var much = Object.keys(data.data).length;
                  console.log(much);
                  $scope.quantity = 3;
                  
                   
                  if(much > $scope.quantity){
                     
                     $scope.styleThis ="btn btn-primary hidden-xs ng-binding";
                     $scope.shoMore = "View More";
                  }
                  
                  $scope.getMore = function() {
                    $scope.press2 = "true";
                   
                    if($scope.quantity >= much){
                      
                        $scope.shoMore = "";
                        $scope.styleThis = "";
                    
                    }else{

                        $scope.quantity += 3;
                        $scope.shoLess = "View Less";
                        $scope.shoMore = "View More";
                        $scope.styleLess = "btn btn-primary hidden-xs ng-binding";
                    }
                  }
                  $scope.getLess = function(){
                      $scope.press3 = "true";
                      if($scope.quantity <= 3){
                        $scope.styleLess = "";
                        $scope.shoLess = "";
                        $scope.styleThis ="btn btn-primary hidden-xs ng-binding";
                        $scope.shoMore = "View More";
                      }else{
                        $scope.quantity -= 3;
                      }
                    
                   
                  }
                 
                 
                });
          }
      }
}]);






