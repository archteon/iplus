jQuery(document).ready(function($){

    $("img.lazy, div.lazy").lazyload({effect: "fadeIn"});

    if($.isFunction($('#content').infinitescroll)){

        $('#content').infinitescroll({
                    loading:{
                        img: '/wp-content/themes/iplus/img/ajax-loader.gif',
                        finishedMsg: '<em>There are no more items to load</em>',
                        msgText: "<em>Please, wait ...</em>"
                    },
                    state:{
                        currPage:1
                    },
                    navSelector             : "#loadmore",
                    nextSelector            : "a.loadmore",
                    itemSelector            : "#content article.post,#content div.post",
                    debug		 	: false,
                    dataType	 	: 'html',
                    behavior		: 'twitter',
                    pathParse       : function(pathStr, nextPage){
                        var path = pathStr.match(/(.*\/page\/)([0-9]+)(\/?)/);
                        var ret = new Array();
                        this.state.currPage = parseInt(path[2]) - 1;
                        ret.push(path[1]);
                        if(path[3])
                            ret.push(path[3]);
                        return ret;
                    }
        }, function(newElements, data, url){
            $("img.lazy, div.lazy",newElements).lazyload({effect: "fadeIn"});
            //USE FOR PREPENDING
            // $(newElements).css('background-color','#ffef00');
            // $(this).prepend(newElements);
            //
            //END OF PREPENDING

            //window.console && console.log('context: ',this);
            //window.console && console.log('returned: ', newElements);

        });

    }


    //Add custom javascript initialization code here


});