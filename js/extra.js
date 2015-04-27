$(document).ready(function(){
  autocomplete();
});


function autocomplete()
{
    $('#query').typeahead({
      hint: false,
      highlight: true,
      minLength: 1
  },
  {
  name: 'suggestion',
  displayKey: 'Word',
  source: substringMatcher()
  });

}

var substringMatcher = function(strs) {
return function findMatches(q, cb) {

      $.ajax({
    url :"suggest/"+q,
    success : function(data)
    {
      
      cb(data);
    },
    fail : function()
    {
      cb([]);
    }
    });

  }
}
