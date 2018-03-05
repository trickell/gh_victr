// Init the github class
var Github = function() {
  this.type = 'php';
  this.data = {};
  this.method = 'store'
  this.location = 'github.php?m='+this.method;
}

// Methods for our github class
$.extend(Github.prototype, {
  // getPopular : retrieves the most popular 'type' of repositories
  getPopular: function(){
    var loc = gh.location;
    $.get('https://api.github.com/search/repositories?q=language:php&sort=stars&order=desc')
      // On the request completion, post the results to the store method in github.php.
      .done(function(results){
        // Store results to class variable
        gh.data = results;

        // Post data to store method
        $.post(loc, gh.data)
          .done(function(res, err){
            gh.listPopular();
          });
      });
  },

  // listPopular : Loops through all the data and renders it into a table to append
  listPopular: function(){
    var data    = gh.data,
        table = $('<table border=0></table>').attr('id','ghList');

    // Loop through the repositories and create a table to output
    $.each(data.items, function(key, val){

      // Create the visible row and append to table
      var row = $('<tr></tr>').addClass('repo')
        .append("<td>"+ val.id
                +"</td><td>"+ val.name
                +"</td>");
      table.append(row);

      // Create the hidden row that will show when clicked on and append right after
      var row = $('<tr></tr>').addClass('info')
        .append("<td colspan='2'><div class='gridbox'><strong>URL: </strong><p>"+ val.url
            +"</p></div><div class='gridbox'><strong>Created: </strong><p>"+ val.created_at
            +"</p></div><div class='gridbox'><strong>Last Push: </strong><p>"+ val.pushed_at
            +"</p></div><div class='gridbox'><strong>Description: </strong><p>"+ val.description
            +"</p></div><div class='gridbox'><strong>Stars: </strong><p>"+ val.stargazers_count
            +"</p></div></td>");
      table.append(row);
    });

    // Write the content to html
    table.prepend('<tr><th>Repository ID</th><th>Repository Name</th></tr>');
    $("#content").append(table);
  },

});

// initialize the object
var gh = new Github();

// We define it this way so we know jQuery uses the $ syntax.
(function($, undefined){

  // Initialize everything
  gh.getPopular();

  // Handle the click to show information
  $('body').on('click', "tr.repo", function(){
    $(this).next('tr').fadeToggle(300);
  });

})(jQuery);
