/**
 * User: denis
 * Date: 2013-06-13
 */

var isLoading = false;  // indicates if the loading screen from loading() is active.
function loading(){
    isLoading = true;
    // add the overlay with loading image to the page
    var over = '<div id="overlay">' +
        '<img id="loading" src="img/loader.gif" alt="loading"/>' +
        '</div>';
    $(over).appendTo('body');

    // click on the overlay to remove it
    $('#overlay').click(function() {
        $(this).remove();
        isLoading = false;
    });

    // hit escape to close the overlay
    $(document).keyup(function(e) {
        if (e.which === 27) {
            $('#overlay').remove();
            isLoading = false;
        }
    });
}


function endLoading(){
    isLoading = false;
    $('#overlay').delay(250).queue(function(){
        $(this).remove();
        $(this).dequeue();
    });
}


function markArticle(article_id){
    var isMarked;
    if($('#'+article_id+' .badge').hasClass('badge-warning')){
        console.log('Article:'+article_id+' was NOT marked.');
        isMarked = 1;

    } else {
        console.log('Article:'+article_id+' was marked.');
        isMarked = 0;

    }

    $.ajax({
        type: 'POST',
        url: './php_scripts/mark_feed_article.php?x='+nocache(),
        data: {
            'marked' : isMarked,
            'article_id' : article_id
        },
        cache: false,
        beforeSend:function(){},
        success:function(data){
//            successful request;
            if(data == 0){          // Un-mark article
                $('#'+article_id+' .badge').removeClass('badge-warning');
                $('.article .badge input:checkbox').prop('checked', false);
                console.log('Article:'+article_id+' is now UN-marked.');

            }else if(data == 1){    // Mark article
                $('#'+article_id+' .badge').addClass('badge-warning');
                $('.article .badge input:checkbox').prop('checked', true);
                console.log('Article:'+article_id+' is NOW marked.');
//
            } else {                // Problem?
                console.log('Problem marking article:'+article_id+' as marked');
                console.log(data);
            }
        },
        error:function(){}
    });
}


var loadCount = 0;      // counts the amount of times the loadRss() function tries to load.
function loadRss(feed_name, feed_id){

    if(feed_id == undefined){
        return;     // exit function
    }else if(feed_id == 0){     // For refreshing the mark articles page
        console.log('Loading Marked articles.');
        displayRss(feed_id);
        return;
    }

    if(feed_name == ''){
        feed_name = $('.brand').text().slice(12);
    }

    $.ajax({
        type: 'POST',
        url: './php_scripts/update_feed.php?x='+nocache(),
        data: {
            'feed_id' : feed_id
        },
        cache: false,
        beforeSend:function(){
            if(!isLoading)   loading();
            console.log('Loading feed: '+feed_id);
            $('.brand').text( 'RSS Reader: '+feed_name);
            $(this).css({color: '#F00'});
            loadCount++;
        },
        success:function(data){
//          successful request
            if(data == 1){              // Success
                loadCount = 0;
                displayRss(feed_id);    // Load RSS feed articles
            } else if(loadCount > 3){
                loadCount = 0;
                alert('Couldn\'t load the feed.');
                endLoading();
            } else {                    // Something bad happened
                console.log('something bad happened while updating feed.... oops!');
                console.log(data);
                setTimeout(loadRss(feed_name, feed_id), 5000);
            }
        },
        error:function(){               // Something worse happened
            console.log('*** Error loading RSS ***');
            console.log('something bad happened while updating feed.... oops!');
            if(typeof data != 'undefined')  console.log(data);
            else                            console.log('data undefined');
            endLoading();
            loadCount = 0;
        }
    });

}


var activeFeed = 0;     // takes the ID value of the feed that is active/open.
function displayRss(feed_id){
    $.ajax({
        type: 'POST',
        url: './php_scripts/load_feed.php?x='+nocache(),
        data: {
            'feed_id' : feed_id
        },
        cache: false,
        beforeSend:function(){
            // remove articles currently displayed
            $('#feed_display li').remove();
            removeArticle();
            activeFeed = feed_id;
            $('.pull-right').show();
        },
        success:function(data){
//          successful request
            if(data == 0){        // Not a valid RSS feed
                endLoading();
                var empty = '<li class="empty_feed">Feed has nothing new at this moment...<br/><br/>Sorry ( ; _ ; )</li>';
                $(empty).appendTo( $('#feed_display') );
            } else {        // Success
                // display new articles
                $(data).appendTo( $('#feed_display') );
                endLoading();
                setArticleStampLeft();
            }
        },
        error:function(){
            console.log('*** Error displaying Feed ***');
            endLoading();
        }
    });
}


var activeArticle = 0;     // takes the ID value of the article that is active/open.
function displayArticle(article_id){
    if(activeArticle == article_id){
        removeArticle();
        return;     // exit function
    }
    $.ajax({
        type: 'POST',
        url: './php_scripts/load_article.php?x='+nocache(),
        data: {
            'article_id' : article_id
        },
        beforeSend:function(){
            console.log('Loading article: '+article_id);
            removeArticle();
            if(!$('#'+article_id).hasClass('read')){
                // If not read, mark read.
                markRead(article_id, 0);
            }
        },
        success:function(data){
//            successful request
            if(data == 0){        // Not a valid Article
                alert('Article not available');
            } else {        // Success
//              display article
                console.log('loading article');
                $('#'+article_id).after(data);
                activeArticle = article_id;
                $('.article iframe').width( $('.article').width()-10 );
                $.each($('.article iframe'), function(){
                    var popout = '<br/><a href="'+$(this).attr('src')+'" title="Click to open in a new window" target="_blank">Popout</a><br/>';
                    $(this).after(popout);
                });
                $('.article a:link').attr('target', '_blank');      // All article links should open in a new tab.
                $('#feed_display').scrollTo('.article');
                console.log('article loaded');
            }

        },
        error:function(){
            console.log('Error displaying Article');
        }
    });
}


function removeArticle(){
    $('.article').remove();
    activeArticle = 0;
}


function markRead(article_id, isRead){
    if(isRead == undefined){
        if($('#'+article_id).hasClass('read')){
            console.log('Article:'+article_id+' has been read.');
            isRead = 1;

        } else {
            console.log('Article:'+article_id+' has NOT been read.');
            isRead = 0;

        }
    }

    $.ajax({
        type: 'POST',
        url: './php_scripts/read_feed_article.php?x='+nocache(),
        data: {
            'viewed' : isRead,
            'article_id' : article_id
        },
        cache: false,
        beforeSend:function(){},
        success:function(data){
//            successful request;
            if(data == 0){          // Un-mark article read
                $('#'+article_id).removeClass('read');
                console.log('Article:'+article_id+' is UN-marked.');

            }else if(data == 1){    // Mark article read
                $('#'+article_id).addClass('read');
                console.log('Article:'+article_id+' is NOW marked.');
//
            } else {                // Problem?
                console.log('Problem marking article:'+article_id+' as read');
                console.log(data);
            }
        },
        error:function(){}
    });
}


function markFeedRead(feed_id, period){
    $.ajax({
        type: 'POST',
        url: './php_scripts/read_feed.php?x='+nocache(),
        data: {
            'period' : period,
            'feed_id' : feed_id
        },
        cache: false,
        beforeSend:function(){
            removeArticle()
        },
        success:function(data){
//            successful request;
            if(data == 0){
                console.log('Failed to mark items read');
            }else if(data == 1){
                displayRss(feed_id);
            }
        },
        error:function(){}
    });
}

function setArticleStampLeft(){
    var mainDisplayWidth = parseInt($('#feed_display').css('margin-left')) + $('#feed_display li').width();
    $.each($('.article_stamp'), function(index, elem){ 
        $(elem).css({'left': mainDisplayWidth-$(elem).width() });
    });
}
