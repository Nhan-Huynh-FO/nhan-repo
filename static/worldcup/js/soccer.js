define(function() {
    var Polling = (function() {
        var REQUEST_TIMEOUT = 15000;
        var POLLING_DELAY = 60 * 1000; // should be 60 seconds

        var disabled = false;
        var pollingErrorCount = 0;

        var updateUri;
        var payloadHandler, emptyHandler, errorHandler, invalidBlockHandler;

        function getLatestMatchInfo() {
            // Construct request URI using offset if present
            var requestUri = updateUri;

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
                dataType: 'json',
                success: function(data) {
                    pollingErrorCount = 0;
                    if (data.match) {
                        payloadHandler(data.match);
                    } else {
                        emptyHandler();
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
                getLatestMatchInfo();
            }
        }

        function pollLater() {
            if (!disabled) {
                var errorDelay = POLLING_DELAY * pollingErrorCount;
                setTimeout(getLatestMatchInfo, POLLING_DELAY + errorDelay);
            }
        }

        function init(options) {
            currentId = options.currentId;
            updateUri = options.updateUri;
            payloadHandler = options.payloadHandler;
            emptyHandler = options.emptyHandler;
            errorHandler = options.errorHandler;

            pollNow();
        }

        return {
            init: init,
            pollNow: pollNow,
            pollLater: pollLater
        };
    }());

    //check live news is active or no
    var liveActive = $('#box_details_news').data('liveblog-active');
    //uri update
    var updateUri   = $('#container_tab_live').data('soccer-uri');
    var timeId      = $('#time-socre');
    var score       = $('#score-data');
    var cardMain    = $('#main-score-card');
    // Check if live blog is active
    if (liveActive && updateUri) {
        Polling.init({
            updateUri: updateUri,
            payloadHandler: function(payload) {
                timeId.text(payload.time);
                score.text(payload.score);
                cardMain.html(payload.card);
                // TODO: should we pollNow() to find more blocks immediately?
                Polling.pollLater();
            },
            emptyHandler: Polling.pollLater,
            errorHandler: Polling.pollLater
        });
    }
});