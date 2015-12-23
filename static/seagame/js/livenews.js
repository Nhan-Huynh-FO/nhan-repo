define('observable', function() {
    /** Mini-Observable helper to wrap values and allow subscribing to
     *  changes made to them.
     */
        // TODO: this probably belongs somewhere else - where?.
    function Observable(initialVal) {
        var value = initialVal;
        var subscribers = [];

        function set(newValue) {
            var oldValue = value;
            if (oldValue !== newValue) {
                value = newValue;
                notifySubscribers(newValue, oldValue);
            }
        }

        function get() {
            return value;
        }

        function subscribe(callback) {
            subscribers.push(callback);

            // call with initial value
            callback(value);

            return {
                dispose: function(){ removeSubscriber(callback); }
            };
        }

        function removeSubscriber(callback) {
            for (var i = 0, l = subscribers.length; i < l; i++) {
                if (subscribers[i] === callback) {
                    subscribers.splice(i, 1);
                    break;
                }
            }
        }

        function notifySubscribers(newValue, oldValue) {
            for (var i = 0, l = subscribers.length; i < l; i++) {
                try {
                    subscribers[i](newValue, oldValue);
                } catch(e) {}
            }
        }

        return {
            get: get,
            set: set,
            subscribe: subscribe
        };
    }

    return Observable;
});
define('resize', function(){
    var Resize = function() {
        var delayTimer = 0;

        function delayFireOnce(timeout) {
            var d = $.Deferred();
            clearTimeout(delayTimer);

            delayTimer = setTimeout(function() {
                d.resolve();
            }, timeout);

            return d.promise();
        }

        /**
         * Get Id of latest block
         */
        function resizeImage(container) {
            var tableObj = container ? $(container).find('table:not(.live_quote)') : $('.text_live table:not(.live_quote)');
            var width_global = 0;
            var pwidth = $('.text_live').width();

            var checkalign = function(obj, scale) {
                if ($(obj).attr('align')) {
                    switch ($(obj).attr('align')) {
                        case 'left':
                            $(obj).css('margin-right', '10px');
                            break;
                        case 'right':
                            $(obj).css('margin-left', '10px');
                            break;
                    }
                }
                return scale;
            };

            var fcondition1 = function(obj) {
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    if (iwidth > pwidth) {
                        $(obj)
                            .width('100%')
                            .parents('table')
                            .width('100%');
                    } else {
                        var scale = (iwidth / pwidth) * 100;
                        scale = checkalign($(obj).parents('table'), scale);
                        $(obj)
                            .css('width', '')
                            .parents('table')
                            .width(scale + '%');
                    }
                    $(obj).attr({ 'data-width': iwidth, 'data-pwidth': pwidth });
                };
                img.src = $(obj).attr('src');
            };

            var fcondition2 = function(obj) {
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    width_global = iwidth >= width_global ? iwidth : width_global;
                    if (iwidth > pwidth) {
                        $(obj)
                            .width(pwidth);
                    } else {
                        $(obj)
                            .css('width', '');
                    }
                    if (width_global < pwidth) {
                        var scale = (width_global / pwidth) * 100;
                        scale = checkalign($(obj).parents('table'), scale);
                        $(obj)
                            .parents('table')
                            .width(scale + '%');
                    }
                    $(obj).attr({ 'data-width': iwidth, 'data-pwidth': pwidth });
                };
                img.src = $(obj).attr('src');
            };

            var fcondition3 = function(obj) {
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    $(obj).attr({ 'data-width': iwidth, 'data-pwidth': pwidth });
                    //if (iwidth > pwidth) {
                    fcondition31(obj);
                    //}
                };
                img.src = $(obj).attr('src');
            };

            var fcondition31 = function(obj) {
                if ($(obj).attr('data-width') > pwidth) {
                    $(obj)
                        .width('100%')
                        .parents('table')
                        .width('100%');
                } else {
                    var scale = ($(obj).attr('data-width') / pwidth) * 100;
                    scale = checkalign($(obj).parents('table'), scale);
                    $(obj)
                        .css('width', '')
                        .parents('table')
                        .width(scale + '%');
                }
            };

            var fcondition4 = function(obj, count_td) {
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    var td_width = (pwidth / count_td);
                    var scale = (iwidth / pwidth) * 100;
                    if (iwidth >= td_width) {
                        $(obj)
                            .width('100%')
                            .parents('td')
                            .width(scale + '%');
                    }
                    $(obj).attr({ 'data-width': iwidth, 'data-pwidth': pwidth });
                };
                img.src = $(obj).attr('src');
            };

            tableObj.each(function(i, o) {
                var isOK = $(o).find('div[data-component="true"]').size() < 1
                    && $(o).parents('div[data-component="true"]').size() < 1
                    && $(o).find('div.thumb_quote').size() < 1
                    && $(o).parents('div.thumb_quote').size() < 1;
                if (isOK) {
                    $(o)
                        .removeAttr('width')
                        .find('img')
                        .css('height', '');
                    var condition = $(o).find('tr').size() > 1;
                    if (condition) {
                        var count_img = count_td = 0;
                        var condition1 = condition2 = false;

                        $(o).find('tr').each(function() {
                            count_td = $(this).find('td').size();
                            $(this).find('td').each(function() {
                                count_img += $(this).find('img').size();
                                if ($(this).find('img').size() > 1) {
                                    condition2 = true;
                                }
                            });
                        });

                        condition1 = count_img < 2;
                        if (condition1) {
                            $(o).find('img').each(function() {
                                fcondition1(this);
                            });
                        } else {
                            if (condition2) {
                                $(o).find('img').each(function() {
                                    fcondition2(this);
                                });
                            } else {
                                $(o).find('img').each(function() {
                                    fcondition4(this, count_td);
                                });
                            }
                        }
                    } else {
                        $(o).find('img').each(function() {
                            fcondition3(this);
                        });
                    }
                }
            });
        }

        function resizeSocial(container) {
            var containerSocialFB = container ? $(container).find('.fb-post') : $('.text_live .fb-post');
            var pwidth = $('.text_live').width();
            containerSocialFB.each(function() {
                $(this).attr('data-width', pwidth);
            });
            if (FB && FB.XFBML && FB.XFBML.parse) {
                FB.XFBML.parse();
            }
        }

        function resize(container) {
            resizeImage(container);
            resizeSocial(container);
        }

        return {
            delayFireOnce: delayFireOnce,
            resizeImage: resizeImage,
            resizeSocial: resizeSocial,
            resize: resize
        };
    };
    return Resize;
});
define('block', ['observable', 'resize'],function(Observable, Resize){
    var Blocks = function(options) {
        var visibleBlocks;
        var hiddenBlocks = $();
        var allBlocks;
        var relativeTime;
        var serverTime;

        var first = new Observable();
        var last = new Observable();
        var hiddenCount = new Observable();
        var visibleCount = new Observable();
        var latestOffsetId = new Observable();
        var dayTimeMore	= new Observable();

        var ANIMATION_SPEED = 300;
        var liveBlocksContainer;
        var sortLatestFirst;
        var sortHighLight;
        var updateWrapper;
        var autoRefresh;
        var hasHighLight;
        var parserAlbum;
        var resizeImage;
        var lastReadMarkBlock = new Observable();

        function updateSortOrder() {
            var latestFirst = sortLatestFirst.get();
            var currentlyLatestFirst = !liveBlocksContainer.hasClass('reversed');

            // quit early if already in the right order (e.g. initial load)
            if (latestFirst === currentlyLatestFirst) {
                return;
            }

            // set default day time public block
            dayTimeMore.set(-1);

            liveBlocksContainer.fadeOut(ANIMATION_SPEED, function() {
                // TODO: ideally reverse from the model, rather than the DOM
                // this looks pretty hacky: it's to "fix" iframes without src attributes
                // (like twitter embeds) which don't reload properly when moved in the DOM
                // this code grabs their inner HTML then reassigns it after moving them.
                liveBlocksContainer.children('.block').each(function(i, theBlock) {
                    var block = $(theBlock);

                    if (block.is('.day')) {
                        block.remove();
                        return;
                    }
                    var emptyIframes = block.find('iframe:not([src])');
                    var iframeHTML = [];

                    emptyIframes.each(function(j, iframe) {
                        var html = $(iframe).contents().find('html').html();
                        iframeHTML[j] = html;
                    });
                    block.prependTo(liveBlocksContainer);

                    window.setTimeout(function(){
                        emptyIframes.each(function(j, iframe) {
                            $(iframe).contents().find('html').html(iframeHTML[j]);
                        });
                    }, 300);
                });

                // move update banner to the bottom
                if (latestFirst) {
                    updateWrapper.prependTo(liveBlocksContainer);
                } else {
                    updateWrapper.appendTo(liveBlocksContainer);
                }

                // set sort order
                liveBlocksContainer.toggleClass('reversed', !latestFirst);

                liveBlocksContainer.fadeIn(ANIMATION_SPEED);

                if (relativeTime) {
                    liveBlocksContainer.children('.block').each(function(i, theBlock) {
                        //update time
                        if(($(theBlock).css('display') != 'none')) {
                            processBlock(theBlock, latestFirst);
                        }
                    });

                    var dayTimeShow = $('.day_time', liveBlocksContainer);
                    if (dayTimeShow.length == 1)
                    {
                        index = $('li',liveBlocksContainer).index($(dayTimeShow[0]).parent());
                        if (sortLatestFirst.get()) {
                            index==1?dayTimeShow.parent().hide():dayTimeShow.parent().show();
                        }else {
                            index==0?dayTimeShow.parent().hide():dayTimeShow.parent().show();
                        }
                    }
                    else {
                        dayTimeShow.parent().show();
                    }
                }
            });
            // set default day time public block
            dayTimeMore.set(-1);
        }

        function updateSortHighLight() {
            var highLight = sortHighLight.get();
            var latestFirst = sortLatestFirst.get();
            var currentlyHighLight = liveBlocksContainer.hasClass('highLight');

            // quit early if already in the right order (e.g. initial load)
            if (highLight === currentlyHighLight) {
                return;
            }
            // set default day time public block
            dayTimeMore.set(-1);

            liveBlocksContainer.fadeOut(ANIMATION_SPEED, function() {
                liveBlocksContainer.children('.block').each(function(i, theBlock) {
                    var block = $(theBlock);

                    if (block.is('.day')) {
                        block.remove();
                        return;
                    }
                    if (highLight && !block.hasClass('high-light')) {
                        block.hide();
                    }
                    else {
                        block.show();
                    }

                    //update time
                    if((block.css('display') != 'none') && relativeTime) {
                        processBlock(theBlock, latestFirst);
                    }
                });
                var dayTimeShow = $('.day_time', liveBlocksContainer);
                if (dayTimeShow.length == 1)
                {
                    index = $('li',liveBlocksContainer).index($(dayTimeShow[0]).parent());
                    if (sortLatestFirst.get()) {
                        index==1?dayTimeShow.parent().hide():dayTimeShow.parent().show();
                    }else {
                        index==0?dayTimeShow.parent().hide():dayTimeShow.parent().show();
                    }
                }
                else {
                    dayTimeShow.parent().show();
                }

                liveBlocksContainer.toggleClass('highLight', highLight);
                liveBlocksContainer.fadeIn(ANIMATION_SPEED);
            });
        }

        /**
         * Get Id of latest block
         */
        function readBlockId(block) {
            var latestBlockIdAttr = block.attr('id');
            return latestBlockIdAttr && latestBlockIdAttr.match(/^block-(.+)/)[1];
        }

        function concat(block1, block2) {
            return $(block1.get().concat(block2.get()));
        }

        function parseBlocks(data, blockOffset)
        {
            // Extract blocks from the returned ajax HTML
            // NB: Filter is used due to text nodes within data returned
            var blocks = $(data).filter('.block');

            // Check that the response contained valid blocks
            if (blocks.length === 0) {
                return;
            }

            if (blockOffset) {
                // partial update, inject new blocks
                if (autoRefresh.get()) {
                    revealBlocks(blocks);
                } else {
                    storeHiddenBlocks(blocks);
                }
            } else {
                // full update, replace all blocks
                resetBlocks(blocks);
            }
        }

        /**
         *  Slide the given blocks into the list of blocks
         */
        function revealBlocks(blocks) {
            // as we change the list of blocks, we also kill all invalid blocks
            var invalidBlocks = allBlocks.filter('.invalid');
            invalidBlocks.remove();
            allBlocks = allBlocks.not('.invalid');

            // FIXME: hack to ensure blocks are in the DOM to measure them (but
            // they might already be in that hidden wrapper - find a better way)
            updateWrapper.prepend(blocks);

            var totalHeight = 0;

            blocks.each(function(index, elm) {
                totalHeight += $(elm).outerHeight();
                //resize image and social
                window.setTimeout(function(){
                    resizeImage(elm);
                    //Google++
                    if ($(elm).find('.g-post').length) {
                        gapi.post.go("text_live");
                    }
                }, 300);
            });

            // set last read mark before we insert new blocks
            lastReadMarkBlock.set(first.get());
            // set not high light
            sortHighLight.set(false);

            visibleBlocks = concat(blocks, visibleBlocks);
            updateObservables();

            updateWrapper.animate({
                height: totalHeight
            }, ANIMATION_SPEED, 'linear', function() {
                moveBlocks(blocks);
                $(this).css('height', 0);
                blocks.fadeIn(ANIMATION_SPEED);
                if (hasHighLight.get()) {
                    $('.block_filter_live .highlight_filter').show();
                }
            });
            parserAlbum();
            // Show button share
            showButtonShare();

        }

        function moveBlocks(blocks) {
            if (sortLatestFirst.get()) {
                blocks.insertAfter(updateWrapper);
            } else {
                blocks = $(blocks.get().reverse());
                blocks.insertBefore(updateWrapper);
            }
        }

        function showButtonShare() {
            // Show button share
            liveBlocksContainer.find('li.block').each(function(){
                var social = $(this).find('div[data-component-type="social"]');
                if (social.length) {
                    $(this).find('div.social_share').remove();
                }
                $(this).hover(function(){
                    liveBlocksContainer.find('.social_share .content_inner').hide();
                    $(this).find('.social_share .content_inner').fadeIn(ANIMATION_SPEED);
                }, function() {
                    liveBlocksContainer.find('.social_share .content_inner').hide();
                });
            });
        }
        function resetBlocks(blocks) {
            // remove current blocks on the page
            updateWrapper.children('.block').remove();
            liveBlocksContainer.children('.block').remove();

            // reset blocks in blocksCollection
            clearHiddenBlocks();
            replaceVisible(blocks);
            lastReadMarkBlock.set(null);

            // insert blocks into the page
            moveBlocks(blocks);
        }

        /**
         *   Inject hidden blocks onto page in the current sort order.
         */
        function storeHiddenBlocks(blocks) {
            blocks.hide();
            updateWrapper.prepend(blocks);
            prependHidden(blocks);
        }

        function prependHidden(newBlocks) {
            hiddenBlocks = concat(newBlocks, hiddenBlocks);
            updateObservables();
        }
        function replaceVisible(newBlocks) {
            visibleBlocks = newBlocks;
            updateObservables();
        }
        function unhighlightNewBlocks() {
            var FADE_DELAY = 1500;
            var FADE_SPEED = 3000;
            var blocks = $(this);

            function fadeBackgroundColour() {
                blocks.animate({
                    backgroundColor: 'red'
                }, FADE_SPEED);
            }

            setTimeout(fadeBackgroundColour, FADE_DELAY);
        }

        function invalidateBlock(blockId) {
            var block = findBlock(blockId);

            if (block && !block.hasClass('invalid')) {
                block.addClass('invalid');
                updateObservables();
            }
        }
        function findBlock(blockId) {
            var wrappedBlock;
            for (var i = 0, l = allBlocks.length; i < l; i++) {
                wrappedBlock = $(allBlocks[i]);
                if (readBlockId(wrappedBlock) === blockId) {
                    return wrappedBlock;
                }
            }
            return wrappedBlock;
        }

        function showHiddenBlocks() {
            var hiddenBlocks = clearHiddenBlocks();
            if (hiddenBlocks.length > 0) {
                revealBlocks(hiddenBlocks);
                $("html, body").animate({
                    scrollTop: (updateWrapper.offset().top) - 50
                }, 900);
            }
            sortHighLight.set(false);
            resizeImage(hiddenBlocks);
        }

        function clearHiddenBlocks() {
            var previousHiddenBlocks = hiddenBlocks;
            hiddenBlocks = $();
            updateObservables();
            return previousHiddenBlocks;
        }

        function updateObservables(init) {
            //set default init
            init = init || false;
            allBlocks = concat(hiddenBlocks, visibleBlocks);

            // FIXME: same block wrapped in different jQuery object
            //        causes more change signals than necessary
            first.set(visibleBlocks.first());
            last.set(visibleBlocks.last());
            hiddenCount.set(hiddenBlocks.length);
            visibleCount.set(visibleBlocks.length);

            // Note: If no valid block, the offset id will be set to undefined
            var validBlocks = allBlocks.not('.invalid');

            if (init) {
                if (sortLatestFirst.get()) {
                    latestOffsetId.set(readBlockId(validBlocks.first()));
                } else {
                    latestOffsetId.set(readBlockId(validBlocks.last()));
                }
            } else {
                latestOffsetId.set(readBlockId(validBlocks.first()));
            }

            hasHighLight.set(allBlocks.is('.high-light'));
        }

        function parse(iso8601)
        {
            var s = $.trim(iso8601);
            s = s.replace(/\.\d\d\d+/,""); // remove milliseconds
            s = s.replace(/-/,"/").replace(/-/,"/");
            s = s.replace(/T/," ").replace(/Z/," UTC");
            s = s.replace(/([\+\-]\d\d)\:?(\d\d)/," $1$2"); // -04:00 -> -0400
            return new Date(s);
        }

        function processBlock(block, latestFirst) {
            var dateTimeBlock    = parse($(block).data('datetime'));
            var dateTimeServer = parse(serverTime);
            var countDayRelative = Math.round(Math.abs(dateTimeBlock - parse(relativeTime)) / (1000 * 60 * 60 * 24));
            var countDayCurrent  = Math.round(Math.abs(dateTimeBlock - dateTimeServer) / (1000 * 60 * 60 * 24));
            countDayCurrent = latestFirst ? countDayCurrent : true;
            /*
             //check latest first block then return
             if (latestFirst && $(block).is('.first') && !liveActive) {
             //dayTimeMore.set(countDayRelative);
             //return;
             }*/
            if (countDayRelative != dayTimeMore.get() && countDayCurrent)
            {
                var strDate = dateTimeBlock.getDate() + "/" + (dateTimeBlock.getMonth()+1);
                if (dateTimeBlock.getFullYear() != dateTimeServer.getFullYear())
                {
                    strDate = strDate + "/" + dateTimeBlock.getFullYear();
                }
                var text = '<li id="block-'+ dateTimeBlock.getTime() +'" class="block day"><div class="day_time">Ngày '+
                    strDate +'</div></li>';

                $(text).insertBefore(block);
                dayTimeMore.set(countDayRelative);
            }
        }

        function init(options) {
            visibleBlocks = options.blocksOnthePage;
            hiddenBlocks = $();
            sortLatestFirst = options.sortLatestFirst;
            updateWrapper = options.updateWrapper;
            autoRefresh = options.autoRefresh;
            sortHighLight = options.sortHighLight;
            hasHighLight = options.hasHighLight;
            parserAlbum = options.parserAlbum;
            resizeImage = options.resizeImage;

            liveBlocksContainer = $('#list_live');
            relativeTime = liveBlocksContainer.data('relative-time');

            serverTime = liveBlocksContainer.data('server-time');
            //update defaule time
            dayTimeMore.set(-1);

            var dayTimeShow = $('.day_time', liveBlocksContainer);
            if (dayTimeShow.length == 1)
            {
                index = $('li',liveBlocksContainer).index($(dayTimeShow[0]).parent());
                if (sortLatestFirst.get()) {
                    index==1?dayTimeShow.parent().hide():dayTimeShow.parent().show();
                }else {
                    index==0?dayTimeShow.parent().hide():dayTimeShow.parent().show();
                }
            }
            else {
                dayTimeShow.parent().show();
            }
            //-----------------------------------------------------
            updateObservables(true);

            // Top and Last blocks depends on the ordering
            var topBlock = new Observable();
            var lastBlock = new Observable();
            function refreshTopBlock() {
                if (sortLatestFirst.get()) {
                    topBlock.set(first.get());
                    lastBlock.set(last.get());
                } else {
                    topBlock.set(last.get());
                    lastBlock.set(first.get());
                }
            }

            first.subscribe(refreshTopBlock);
            sortLatestFirst.subscribe(refreshTopBlock);
            refreshTopBlock();
            //-----------------------------------------------------
            function bindClassToObservableNode(obs, className) {
                obs.subscribe(function(currentNode, previousNode) {
                    if (previousNode) {
                        $(previousNode).removeClass(className);
                    }
                    if (currentNode) {
                        $(currentNode).addClass(className);
                    }
                });
            }

            bindClassToObservableNode(topBlock, 'first');
            bindClassToObservableNode(lastBlock, 'end');
            bindClassToObservableNode(lastReadMarkBlock, 'read-to');
            //-----------------------------------------------------

            // Bind sort order
            sortLatestFirst.subscribe(updateSortOrder);

            // Bind sort high light
            sortHighLight.subscribe(updateSortHighLight);

            // Show button share
            showButtonShare();
        }

        init(options);

        return {
            parseBlocks: parseBlocks,
            showHiddenBlocks: showHiddenBlocks,
            invalidateBlock: invalidateBlock,
            latestOffsetId: latestOffsetId,
            hiddenCount: hiddenCount,
            visibleCount: visibleCount,
            sortHighLight:sortHighLight,
            hasHighLight: hasHighLight
        };
    };
    return Blocks;
});
define('controls', function(){
    function Controls(options) {
        function bindToggles(value, toggleOn, toggleOff, isCount) {
            toggleOn.bind('click', function() {
                var currentlyOn = toggleOn.hasClass('active');
                if (!currentlyOn) {
                    value.set(true);
                }
            });
            toggleOff.bind('click', function() {
                var currentlyOff = toggleOff.hasClass('active');
                if (!currentlyOff) {
                    value.set(false);
                }
            });
            value.subscribe(function(isEnabled) {
                toggleOn.toggleClass('active',  isEnabled);
                toggleOff.toggleClass('active', !isEnabled);
                if (isCount) {
                    $('#live_post_status a').toggleClass('short_live', isEnabled);
                }
            });
        }
        function bindCheckbox(value, checkOn, isCount) {
            checkOn.bind('click', function() {
                var currentlyOn = checkOn.is(':checked');
                if (currentlyOn) {
                    value.set(true);
                }else {
                    value.set(false);
                }
            });
            value.subscribe(function(isEnabled) {
                checkOn.attr("checked", isEnabled);
                value.set(isEnabled);
                if (isCount) {
                    $('#live_post_status a').toggleClass('short_live', isEnabled);
                }
            });
        }
        function init(options) {
            var sortLatestFirst = options.sortLatestFirst;
            var sortHighLight   = options.sortHighLight;
            var hasHighLight	= options.hasHighLight;
            bindToggles(sortLatestFirst, $('.sort-live.latest'), $('.sort-live.oldest'), true);
            if (hasHighLight.get()) {
                $('.main_content_detail .block_filter_live .highlight_filter').show();
            }
            bindCheckbox(sortHighLight, $('.highlight_filter #checkNew'), false);
            $('#live_post_status a').toggleClass('short_live', true);
            $('.main_content_detail .block_filter_live').show();
        }
        init(options);
    }
    return Controls;
});
define('polling', function() {
    var Polling = (function() {
        var REQUEST_TIMEOUT = 15000;
        var POLLING_DELAY = 30 * 1000; // should be 300 seconds

        var disabled = false;
        var pollingErrorCount = 0;

        var updateUri;
        var payloadHandler, emptyHandler, errorHandler, invalidBlockHandler;
        var currentOffset;

        function getLatestBlocks() {
            // Construct request URI using offset if present
            var requestUri = updateUri;
            var offset = currentOffset.get(); // copy for this execution
            if (offset) {
                requestUri += '&offset=' + offset;
            }

            /** Expected behaviour
             *  - query for new blocks
             *  - if successful, call the payloadHandler with the data
             *  - if an error occurs:
             *      content-not-found - nothing to see here anymore, stop polling
             *      block-not-found   - call the invalidBlockHandler, which typically
             *                          resets the offset and triggers a new poll
             *      (other)           - log and keep trying
             */
            $.ajax({
                url: requestUri,
                timeout: REQUEST_TIMEOUT,
                cache: true,
                jsonpCallback: "doSomethingWithJson",
                dataType: 'jsonp',
                success: function(data, textStatus, jqXHR) {
                    // Check if kill switch has been triggered and disable polling
                    if (jqXHR.getResponseHeader('X-GU-AutoRefresh') === 'off') {
                        disable();
                    }

                    if (data.status === 404) {
                        pollingErrorCount += 1;

                        var error = data.error;
                        if (error && error.errorKey === 'content-not-found') {
                            // disable polling altogether
                            disable();
                        } else if (error && error.errorKey === 'block-not-found') {
                            invalidBlockHandler(offset);
                        } else {
                            errorHandler();
                        }
                    } else {
                        pollingErrorCount = 0;

                        if (data.html) {
                            payloadHandler(data.html, offset, data.timer);
                        } else {
                            emptyHandler();
                        }
                    }
                },
                error: function() {
                    // can apparently happen, e.g. request times out
                    errorHandler();
                }
            });
        }

        function disable() {
            disabled = true;
        }

        function pollNow() {
            if (!disabled) {
                getLatestBlocks();
            }
        }

        function pollLater() {
            if (!disabled) {
                var errorDelay = POLLING_DELAY * pollingErrorCount;
                setTimeout(getLatestBlocks, POLLING_DELAY + errorDelay);
            }
        }

        function init(options) {
            currentOffset = options.currentOffset;
            updateUri = options.updateUri;
            payloadHandler = options.payloadHandler;
            emptyHandler = options.emptyHandler;
            errorHandler = options.errorHandler;
            invalidBlockHandler = options.invalidBlockHandler;

            pollLater();
        }

        return {
            init: init,
            pollNow: pollNow,
            pollLater: pollLater
        };
    }());

    return Polling;
});
define('update.bar', function() {
    var UpdateBar = (function() {
        var updateBanner;
        var updateWrapper;
        var updateCounterElm;
        var ANIMATION_SPEED = 400;
        var counter;

        function showOrUpdate() {
            var hidden = updateBanner.css('display') === 'none';
            if (hidden) {
                // show
                var updateBannerHeight = updateBanner.outerHeight();
                updateCounterText(true);
                updateBanner.show();
                updateWrapper.animate({
                    height: updateBannerHeight
                }, ANIMATION_SPEED);
            } else {
                // update
                updateCounterText(true);
            }
        }

        function hide() {
            updateBanner.fadeOut(ANIMATION_SPEED, function() {
                updateCounterText();
            });
        }

        /* Animate out/in any update to the counter, so users notice change. */
        function updateCounterText(animate) {
            if (animate) {
                updateCounterElm.fadeOut(ANIMATION_SPEED, function() {
                    $(this).text(counter.get());
                    $(this).fadeIn(ANIMATION_SPEED);
                });
            } else {
                updateCounterElm.text(counter.get());
            }
        }

        function init(options) {
            counter = options.counter;
            updateWrapper = options.updateWrapper;
            autoRefresh = options.autoRefresh;
            var clickHandler = options.clickHandler;

            updateBanner = $('#live_post_status');
            updateCounterElm = $('#live-updates-counter');
            updateBanner.bind('click', clickHandler);

            counter.subscribe(function(count) {
                if (count > 0) {
                    showOrUpdate();
                } else {
                    hide();
                }
            });
        }
        return {
            init: init
        };
    }());

    return UpdateBar;
});
define('update.title', ['jquery.visibility'], function() {
    /**
     * @fileoverview Utility for alerting users of updates to page when hidden
     */
    return (function() {
        var originalDocumentTitle = document.title;
        var blockCount = 0;
        // Assumption that user is viewing the page by default and has not
        // opened the page in a hidden tab eg. Middle clicking a link.
        var isPageVisible = true;

        /**
         * Change the document's title with new block count.
         * @param {number} currentBlockCount Current total block count.
         * @param {number} previousBlockCount Previous total block count.
         */
        function updateHiddenPage(currentBlockCount, previousBlockCount) {
            // Subscribe object can sometimes return undefined ie. Empty blog.
            if (isPageVisible ||
                typeof currentBlockCount !== 'number' ||
                typeof previousBlockCount !== 'number'
                ) {
                return;
            }

            var diffCount = currentBlockCount - previousBlockCount;
            // Ensure update count only goes up, not down such as when blocks
            // are deleted.
            if (diffCount > 0) {
                blockCount += diffCount;
                document.title = '(' + blockCount + ') ' + originalDocumentTitle;
            }
        }

        function setupHiddenPage() {
            blockCount = 0;
            isPageVisible = false;
        }

        function resetVisiblePage() {
            isPageVisible = true;
            document.title = originalDocumentTitle;
        }

        /**
         * Initialise subscribes to block count and page visibility changes.
         * @param {object} obBlockCount Observable block count.
         */
        function init(obBlockCount) {

            // Update every time block count changes.
            obBlockCount.subscribe(updateHiddenPage);

            // Use jQuery visibility plug-in to bind show/hide events.
            $(document).on({
                'show.visibility': resetVisiblePage,
                'hide.visibility': setupHiddenPage
            });
        }

        return {
            'init': init
        };
    }());
});
/*global twttr:true */
define('embed',['twitter','facebook','jquery.inview'], function(twttr, FB) {

    // workaround way to detect if the current tab has focus
    // for some reason, trying to load rich tweet embeds when the page isn't focused
    // seems to break and leave them blank. adding a 3s delay when unfocused fixes this.
    function tabIsFocused() {

        var hidden;

        // first we try using the new visibility API
        // https://developer.mozilla.org/en-US/docs/DOM/Using_the_Page_Visibility_API
        if (typeof document.hidden !== "undefined") {
            hidden = "hidden";
        } else if (typeof document.mozHidden !== "undefined") {
            hidden = "mozHidden";
        } else if (typeof document.msHidden !== "undefined") {
            hidden = "msHidden";
        } else if (typeof document.webkitHidden !== "undefined") {
            hidden = "webkitHidden";
        }

        // no support for visibility API in IE < 10 or Safari (any)
        // so fall back to document.hasFocus() -- which is broken in chrome!
        // https://code.google.com/p/chromium/issues/detail?id=64846
        if (!hidden) {
            // fixes opera, which doesn't have this
            if (typeof document.hasFocus === 'undefined') {
                document.hasFocus = function () {
                    return document.visibilityState === 'visible';
                };
            }
            return document.hasFocus();
        }

        var isHidden = document[hidden];
        return !isHidden;
    };

    function toggleTweets() {
        // this allows us to dynamically add embedded tweets rather than on pageload
        $('.main_content_detail').on('inview', '.container_social blockquote', function(event, isVisible) {
            var delay = 300;
            if (!tabIsFocused()) {
                // 3 seconds is somewhat arbitrary but seems to be enough to avoid race condition
                delay = 3000;
            }
            if (isVisible && twttr.widgets !== undefined) {
                var elm = $(this);
                elm.addClass('twitter-tweet');
                window.setTimeout(function(){
                    twttr.widgets.load();
                }, delay);
            }
        });
        // this allows us to dynamically add embedded tweets rather than on pageload
        $('.main_content_detail').on('inview', '.container_social .twitter-tweet-rendered', function(event, isVisible) {
            var delay = 300;
            var pwidth = $('.text_live').width();
            if (isVisible && twttr.widgets !== undefined) {
                var elm = $(this);
                window.setTimeout(function(){
                    elm.css('width', pwidth);
                }, delay);
            }
        });
    };

    function toggleFB() {
        $('.main_content_detail').on('inview', '.fb-post a', function(event, isVisible) {
            var delay = 300;
            if (!tabIsFocused()) {
                // 3 seconds is somewhat arbitrary but seems to be enough to avoid race condition
                delay = 3000;
            }
            if (isVisible && FB.XFBML !== undefined) {
                window.setTimeout(function(){
                    FB.XFBML.parse();
                }, delay);
            }
        });
    };
    function toggleCmtWidget() {
        $('.main_content_detail').on('inview', 'div[data-component-type]', function (e, isVisible) {
            var delay = 300;
            if (!tabIsFocused()) {
                // 3 seconds is somewhat arbitrary but seems to be enough to avoid race condition
                delay = 3000;
            }
            if (isVisible && 'undefined' != typeof Parser) {
                var elm = $(this);
                window.setTimeout(function(){
                    Parser.parseAll();
                    elm.removeAttr('data-component-type');
                }, delay);
            }
        });
    };

    return {
        toggleTweets: toggleTweets,
        toggleFB: toggleFB,
        toggleCmtWidget: toggleCmtWidget
    };
});
define(['observable','block','controls', 'polling', 'update.bar', 'update.title', 'embed', 'resize', 'parser.album','jquery-scrolltofixed-min'],
    function(Observable, Block, Controls, Polling, UpdateBar, UpdateTitle, Embed, Resize, ParserAlbum){
        var configs = $.extend({}, defaults, {});
        /*
         Block - manager block, Controls - manager block controls,
         Polling - polling data, Update -> update bar and update title,
         Embed -> reload tweets and reload vote, rating...,
         Resize -> resize images
         */

        //check live news is active or no
        var liveActive = $('#box_details_news').data('liveblog-active');
        var sortNewest = $('#box_details_news').data('sort-newest');
        var isHighLight = $('.highlight_filter #checkNew').is(':checked');

        //check sortLatestFirst, if live active then sort lastest is true, else false
        var sortLatestFirst = new Observable(sortNewest);
        var sortHighLight   = new Observable(isHighLight);

        var autoRefresh 	= new Observable(true);
        var hasHighLight 	= new Observable(false);

        //get all the block on the page
        var blocksOnThePage = $('#list_live').children('.block');
        //dom update wrapper
        var updateWrapper = $('#live-updates-wrapper');
        //uri update
        var updateUri = $('#container_tab_live').data('update-uri');
        //time update
        var timeUpdate = $('#timer_update');

        //init parser album slide show
        var parserAlbum = new ParserAlbum({
            siteId: configs.siteId,
            vneUrl: configs.vneUrl,
            baseUrl: configs.baseUrl,
            platform: configs.platform
        });
        var resizeObject = new Resize();

        //manager Blocks: sort block, count hidden, ...
        var managerBlock = new Block({
            sortLatestFirst: sortLatestFirst,
            sortHighLight: sortHighLight,
            liveActive: liveActive,
            blocksOnthePage: blocksOnThePage,
            updateWrapper: updateWrapper,
            autoRefresh : autoRefresh,
            hasHighLight : hasHighLight,
            parserAlbum: parserAlbum.parse,
            resizeImage: resizeObject.resize
        });

        //manager controls
        (new Controls({
            sortLatestFirst: sortLatestFirst,
            sortHighLight: sortHighLight,
            hasHighLight : hasHighLight
        }));

        //liveActive = false;
        //check if live news is active
        if(liveActive && updateUri) {
            Polling.init({
                currentOffset: managerBlock.latestOffsetId,
                updateUri: updateUri,
                payloadHandler: function(payload, offset, timer) {
                    // Has side effects(?)
                    managerBlock.parseBlocks(payload, offset);
                    // Update timer
                    timeUpdate.html(timer);
                    // TODO: should we pollNow() to find more blocks immediately?
                    Polling.pollLater();
                },
                invalidBlockHandler: function(blockId) {
                    managerBlock.invalidateBlock(blockId);
                    // Note: initially it was set to poll immediately,
                    // but that always hit the browser cache and
                    // re-inserted the same block, which isn't much
                    // use. We wait a bit before polling to exceed the
                    // cache time.
                    Polling.pollLater();
                },
                emptyHandler: Polling.pollLater,
                errorHandler: Polling.pollLater
            });
            // Update bar when scroll
            UpdateBar.init({
                counter: managerBlock.hiddenCount,
                clickHandler: managerBlock.showHiddenBlocks,
                updateWrapper: updateWrapper,
                autoRefresh : autoRefresh
            });
            // Update title browser
            UpdateTitle.init(managerBlock.visibleCount);
        }
        // Annoyingly, wait for document-ready else Omniture might not be loaded...
        $('document').ready(function() {
            // Render all tweets in the view
            Embed.toggleTweets();
            // Render all Facebook
            Embed.toggleFB();
            // Render cmt widget (vote)
            Embed.toggleCmtWidget();

            // Sticky
            if (device_env == 4)
            {
                var offsetComment = 40;
                $('#box_coment_300').scrollToFixed({
                    marginTop: 0,
                    limit: function() {
                        var limit = $('#wrapper_footer').offset().top - $('#box_coment_300').outerHeight(true) - offsetComment;
                        return limit;
                    },
                    minWidth: 1000,
                    zIndex: 999,
                    fixed: function() {  },
                    dontCheckForPositionFixedSupport: true
                });
                $("#box_coment_300 .scroll-pane").niceScroll();
            }

            var $filter = $('.live_post_status');
            var $filterSpacer = $('<div />', {
                "class": "filter-drop-spacer",
                "height": $filter.outerHeight()
            });

            if ($filter.size())
            {
                var offsetTop   = $('.block_filter_live').offset().top;
                var filterWidth = $('.block_filter_live').width();
                $(window).scroll(function ()
                {
                    if (!$filter.hasClass('fix') && $(window).scrollTop() > offsetTop)
                    {
                        $filter.css('width', filterWidth);
                        $filter.before($filterSpacer);
                        $filter.addClass("fix");
                        autoRefresh.set(false);
                    }
                    else if ($filter.hasClass('fix')  && $(window).scrollTop() < $filterSpacer.offset().top)
                    {
                        $filter.removeClass("fix");
                        $filterSpacer.remove();
                        if (managerBlock.hiddenCount.get()) {
                            managerBlock.showHiddenBlocks();
                        }
                        autoRefresh.set(true);
                    }
                });
            }
            //resize image and social
            resizeObject.resize();
            $(window).resize(function() {
                resizeObject.delayFireOnce(1000).done(function() {
                    resizeObject.resize();
                });
            });
            VNE.Comment.getComments(function(total){
                var heightComment = $('#list_comment').height();
                if (heightComment < 280)
                {
                    $('.scroll-pane').css('height',heightComment);
                }
                $('.link_reply').on('click', function(){
                    alert(1);
                    $("#box_coment_300 .scroll-pane").getNiceScroll().onResize();
                    $("#txtComment").focus();
                })
                $('.filter_coment a').on('click', function() {
                    $("#box_coment_300 .scroll-pane").getNiceScroll().doScrollPos(0,0);
                    $("#box_coment_300 .scroll-pane").getNiceScroll().onResize();
                })
                $('.view_more_coment a').on('click', function(){
                    $("#box_coment_300 .scroll-pane").getNiceScroll().onResize();
                })
            });

        });
    });