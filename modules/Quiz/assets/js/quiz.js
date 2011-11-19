var $quiz;
var Helper;

function __log() {
    if (console && console.log) {
        if ($.browser.msie) {
            console.log(arguments);
        } else {
            try {
                console.log.apply(console, arguments);
            } catch (e) {
                console.log(e);
                console.log(arguments);
            }
        }
    }
}

(function($){

    Helper = {
        serialize: function(value, key){
            // nie interpretuje wogule array ..?? ale dziala
            if (null === value){
                return '&';
            } else
            if (true === value) {
                return '1&';
            } else
            if (false === value) {
                return '0&';
            } else
            if (typeof value == 'object') {
                var url = [];
                $.each(value, function(i,e){
                    var k = key === undefined ? i :  key+'['+i+']';
                    if (typeof e == 'object') {
                        url.push(Helper.serialize(e, k));
                    } else {
                        url.push(k+'=' + Helper.serialize(e, k));
                    }
                });
                return url.join('&');
            } else {
                try {
                    return value.toString();
                } catch(e) {
                    return value;
                }
            }
        }
    }

    $quiz = {
        'options': {
            'question_timeout': 20,
            'url': {
                'quiz_data': 'app/getquiz',
                'quiz_finish': 'app/results',
                'quiz_results': 'app/results',
                'user_invite': 'app/userinvite'
            }
        },

        'errors': {
            'on_load': 'Wystąpił problem z w czasie ładowania danych. Proszę <a onclick="window.location.refresh();">odświeżyć</a> stronę ponownie.',
            'on_end': 'Wystąpił problem z w czasie ładowania danych. Proszę <a onclick="window.location.refresh();">odświeżyć</a> stronę ponownie.',
            'firnds_invite_rq': 'Niestety do ponownej rozgrywki, zabrakło tylko paru znajomych, a dokładniej '
        },

        'elements': {

            'message_hello' : '#hello-message',
            'message_begin' : '#begin-message',
            'message_invite': '#invite-message',
            'awards_messege': '#awards-messege',

            'awards_action': '.show-awards-messege',

            'play_box': '#play-box',
            'fb_like': '#fb_like_button',

            // to refactore
            'start_again_info_block' : '#invite-message',

            'results': '#results',
            'question': '#question',
            'question_no': '#question-no',
            'question_time': '#question-time',
            'question_no_placeholder': '.question-number',
            'question_time_placeholder': '.question-time',
            'question_title': '#question_title',
            'answers': '#answers',
            'loader': '#loader',
            'error_container': '#messager'
        },

        /*
         * vars
         */
        'question_no': 0,
        'timer': 0,
        'timeInterval': null,
        'questionPosition': -1,
        'questionsData': null,
        'answersCollection': {},
        'currentQuestionId': null,
        'quizId' : null,
        'auto_start' : false,
        'inviteFriends': 3,
        'isAuth': false,

        'actions':
        {
            'clearAll': function(){
                $quiz.actions.stopTimer();

                $quiz.question_no = 0,
                $quiz.timer = 0,
                $quiz.timeInterval = null,
                $quiz.questionPosition = -1,
                $quiz.questionsData = null,
                $quiz.answersCollection = {},
                $quiz.currentQuestionId = null;
                $quiz.quizId = null;
                $quiz.auto_start = false;
                $quiz.inviteFriends = 3;

            },
            'nextQuestion':function() {
                if (!$quiz.questionsData)
                {
                    __log('nextQuestion.questionsData:', $quiz.questionsData);

                    $quiz.actions.showError(
                        $quiz.errors.on_load
                    );

                    return;
                }

                if (typeof $quiz.questionsData[++$quiz.questionPosition] == 'object')
                {
                    $quiz.actions.setupQuestion(
                        $quiz.questionsData[$quiz.questionPosition]
                    );
                }
                else
                {
                    $quiz.actions.questionEnds();
                }
            },
            'questionEnds': function(){


                $quiz.auto_start = false;
                $quiz.actions.stopTimer();
                $quiz.actions.clearCanvas();
                $quiz.actions.hideControls();

                // $($quiz.elements.question_title).text('Wyniki');

                $quiz.actions.showLoader();

                __log('questionEnds:results' , $quiz.answersCollection);

                var data = {
                    'quizId': $quiz.quizId,
                    'answers': $quiz.answersCollection
                };

                $.ajax({
                    'url' : $quiz.options.url.quiz_finish,
                    'dataType': 'json',
                    'data': Helper.serialize(data),
                    'type': 'POST',
                    //'timeout': 10*1000,
                    'success': function(data) {

                        __log('questionEnds:data:', data);
                        $quiz.actions.hideLoader();

                        if (!data.status) {
                            $quiz.actions.showError(data.message);
                            return;
                        }

                        try
                        {
                            $quiz.actions.renderResult(data.result);
                            $quiz.actions.showInviteMessage();
//                            $($quiz.elements.start_again_info_block).show();
                        } catch (e) {
                            __log('questionEnds:cached:', e);

                            $quiz.actions.showError(
                                $quiz.errors.on_load
                            );
                        }
                    },
                    'error': function() {
                        __log('questionEnds:error:');

                        $quiz.actions.showError(
                            $quiz.errors.on_load
                        );
                    }
                });
            },
            'loadQuestions': function() {

                $quiz.actions.showPlayBox();

                if (null === $quiz.questionsData)
                {
                    $quiz.actions.showLoader();

                    $.ajax({
                        'url' : $quiz.options.url.quiz_data,
                        'dataType': 'json',
                        //'timeout': 10*1000,
                        'success': function(data) {
                            __log('loadQuestions:data:', data);
                            $quiz.actions.hideLoader();

                            if (!data.status) {
                                $quiz.actions.showError(data.message);
                                return;
                            }

                            $quiz.quizId = data.result.quizId;
                            $quiz.questionsData = data.result.questions;

                            try {
                                $quiz.actions.nextQuestion();
                                $quiz.actions.showControls();
                            } catch (e) {
                                __log('loadQuestions:cached:', e);

                                $quiz.actions.showError(
                                    $quiz.errors.on_load
                                );
                            }
                        },
                        'error': function() {
                            __log('loadQuestions:error:');

                            $quiz.actions.showError(
                                $quiz.errors.on_load
                            );
                        }
                    });
                }
            },
            'showLoader': function() {
                $($quiz.elements.loader).show();
            },
            'hideLoader': function() {
                $($quiz.elements.loader).hide();
            },
            'stopTimer': function(){
                if (null !== $quiz.timeInterval) {
                    clearInterval($quiz.timeInterval);
                }
            },
            'startTimer': function() {
                $quiz.actions.stopTimer();

                $($quiz.elements.question_time).text($quiz.options.question_timeout);
                $quiz.timeInterval = setInterval($quiz.actions.updateTimer, 1000);
            },
            'updateTimer': function(){
                var currentTime = $($quiz.elements.question_time).text(),
                    currentTime = parseInt(currentTime);

                if (currentTime <= 0) {
                    $quiz.actions.answerNone();
                    $quiz.actions.nextQuestion();
                } else {
                    $($quiz.elements.question_time).text(--currentTime);
                }
            },
            'getTimerRemain': function(){
                var currentTime = $($quiz.elements.question_time).text(),
                    currentTime = parseInt(currentTime);

                return (currentTime > 0) ? currentTime : 0;
            },
            'setupQuestion': function(question) {

                $quiz.currentQuestionId = parseInt(question.id);

                $quiz.actions.showLoader();

                $($quiz.elements.question_title).text(question.title);
                $($quiz.elements.question_no).text('Pytanie '+ (++$quiz.question_no));

                var q;
                switch(question.type)
                {
                    case 'wideo':
                    case 'video':
                    case 'audio':
                        var videoId = question.content.match(/v=([^?&]+)/)[1];
                        q = '<iframe width="340" height="203" src="http://www.youtube.com/embed/'+ videoId +'?autoplay=1" frameborder="0" allowfullscreen></iframe>';
                        break;

                    case 'image':
                        q = '<img src="upload/'+ question.content+ '" width="340" >';
                        break;

                    case 'text':
                        $($quiz.elements.question_title).text('Odpowiedz na pytanie:');
                        q = '<p>'+ question.content+ '</p>';
                        break;
                }

                var answers = [];

                answers.push('<ul class="answers">');
                $(question.answers).each(function(k, item) {
                    answers.push('<li answerId="'+ item.id +'"><span></span>'+ item.name +'</li>');
                });
                answers.push('</ul>');


                $($quiz.elements.question).html(q);
                $($quiz.elements.answers).html(answers.join("\n"));

                $($quiz.elements.answers).find('li[answerId]').bind('click', $quiz.actions.answerClicked);

                $quiz.actions.hideLoader();
                $quiz.actions.startTimer();
            },
            'getCurrentQuestionId': function() {
                return $quiz.currentQuestionId;
            },
            'answerClicked':function(e) {
                var answerId = $(this).attr('answerId'),
                    answerId = parseInt(answerId);

                var questionId = $quiz.actions.getCurrentQuestionId();

                $quiz.actions.addAnswerForQuestion(answerId, questionId, $quiz.actions.getTimerRemain());
                $quiz.actions.nextQuestion();
            },
            'answerNone': function() {
                $quiz.actions.addAnswerForQuestion(
                    null,
                    $quiz.actions.getCurrentQuestionId(),
                    0
                );
            },
            'addAnswerForQuestion': function(answerId, questionId, second) {
                $quiz.answersCollection[questionId] = {
                    'questionId': questionId,
                    'answerId': answerId,
                    'second': second
                };
            },
            'showError': function(errorMessage) {
                $quiz.actions.hideLoader();
                $($quiz.elements.error_container).html(errorMessage);
                $($quiz.elements.error_container).show();
            },
            'hideError': function() {
                $($quiz.elements.error_container).text('');
            },
            'showControls': function() {
                $($quiz.elements.question_no_placeholder).show();
                $($quiz.elements.question_time_placeholder).show();
            },
            'hideControls': function() {
                $($quiz.elements.question_no_placeholder).hide();
                $($quiz.elements.question_time_placeholder).hide();
            },
            'clearCanvas': function() {
                $($quiz.elements.question_title).text('');
                $($quiz.elements.question_no).text('');
                $($quiz.elements.question).html('');
                $($quiz.elements.answers).html('');
            },
            'showResults': function() {

                $quiz.actions.showLoader();
                $quiz.actions.hideError();
                
                $.ajax({
                    'url' : $quiz.options.url.quiz_results,
                    'dataType': 'json',
                    'type': 'GET',
                    'success': function(data) {

                        __log('showResults:data:', data);
                        $quiz.actions.hideLoader();

                        if (!data.status) {
                            $quiz.actions.showError(data.message);
                            return;
                        }

                        try
                        {
                            $quiz.actions.renderResult(data.result);
                            $quiz.actions.showInviteMessage(true);
                        } catch (e) {
                            __log('showResults:cached:', e);

                            $quiz.actions.showError(
                                $quiz.errors.on_load
                            );
                        }
                    },
                    'error': function() {

                        __log('showResults:error:');

                        $quiz.actions.showError(
                            $quiz.errors.on_load
                        );
                    }
                });
            },
            'renderResult': function(result) {
                var results = [];

                results.push('<ul class="results_list">');
                $(result).each(function(k, item) {
                    results.push('<li><img src="https://graph.facebook.com/'+ item.facebookId +'/picture"><span class="name"> '+ item.fullname +'</span><span class="points"> '+ item.points +'</span></li>');
                });
                results.push('</ul>');

                $($quiz.elements.results).html(results.join("\n"));
            },

            'showHelloMessage': function () {

                if ($quiz.auto_start) {
                    return;
                }

                $quiz.actions.hideLoader();
                $quiz.actions.hideError();
                $($quiz.elements.fb_like).show();
                $($quiz.elements.play_box).hide();
                $($quiz.elements.message_hello).show();
                $($quiz.elements.message_begin).hide();
                $($quiz.elements.message_invite).hide();
            },

            'showBeginMessage': function () {

                if ($quiz.auto_start) {
                    return;
                }

                $quiz.actions.hideLoader();
                $quiz.actions.hideError();
                $($quiz.elements.fb_like).hide();
                $($quiz.elements.play_box).hide();
                $($quiz.elements.message_hello).hide();
                $($quiz.elements.message_begin).show();
                $($quiz.elements.message_invite).hide();
            },

            'showInviteMessage': function (forse) {
                if (true !== forse && $quiz.auto_start) {
                    return;
                }

                $quiz.actions.hideLoader();
                $quiz.actions.hideError();
                $($quiz.elements.fb_like).hide();
                $($quiz.elements.play_box).hide();
                $($quiz.elements.message_hello).hide();
                $($quiz.elements.message_begin).hide();
                $($quiz.elements.message_invite).show();
            },
            'showPlayBox': function() {
                $quiz.actions.hideLoader();
                $quiz.actions.hideError();
                $($quiz.elements.fb_like).hide();
                $($quiz.elements.play_box).show();
                $($quiz.elements.message_hello).hide();
                $($quiz.elements.message_begin).hide();
                $($quiz.elements.message_invite).hide();
            },
            'toggleAwards': function() {
                var d = $($quiz.elements.awards_messege).css('display');
                if (d == 'none') {
                    $($quiz.elements.awards_messege).show();
                    $($quiz.elements.message_invite).hide();
                    $($quiz.elements.awards_action).text('Pokaż wyniki');
                } else {
                    $quiz.actions.showInviteMessage(true);
                    $($quiz.elements.awards_messege).hide();
                    $($quiz.elements.awards_action).text('Zobacz nagrody jakie możesz wygrać');
                }
            },
            'hideCanvas':function(){
                $quiz.actions.hideLoader();
                $($quiz.elements.fb_like).hide();
                $($quiz.elements.play_box).hide();
                $($quiz.elements.message_hello).hide();
                $($quiz.elements.message_begin).hide();
                $($quiz.elements.message_invite).hide();
            },
            'isValidFBFriendRequest': function(frinedInvite) {
                __log('isValidFBFriendRequest:1', arguments);
                if (frinedInvite)
                {
                    __log('isValidFBFriendRequest:2', arguments);
                    if (frinedInvite.to.length < $quiz.inviteFriends)
                    {
                        __log('isValidFBFriendRequest:3', arguments);
                        $quiz.actions.showError(
                            $quiz.errors.firnds_invite_rq . $quiz.inviteFriends
                        );

                        $quiz.inviteFriends -= frinedInvite.to.length;
                    } else {
                        __log('isValidFBFriendRequest:5', arguments);
                        $quiz.actions.validFriendRequest();
                        $quiz.inviteFriends = 3;
                    }
                }
            },
            'validFriendRequest': function() {
                $quiz.auto_start = false;
                __log('validFriendRequest', arguments);
                $quiz.actions.clearAll();
                $quiz.actions.hideCanvas();
                $quiz.actions.showLoader();

                $.ajax({
                    'url' : $quiz.options.url.user_invite,
                    'dataType': 'json',
                    'type': 'POST',
                    'success': function(data) {

                        __log('validFriendRequest:data:', data);
                        $quiz.actions.hideLoader();

                        if (!data.status) {
//                            $quiz.actions.showError(data.message);
                            $quiz.actions.showInviteMessage(true);
                            return;
                        }

                        try
                        {
                            $quiz.actions.loadQuestions();
                        } catch (e) {
                            __log('validFriendRequest:cached:', e);

                            $quiz.actions.showError(
                                $quiz.errors.on_load
                            );
                        }
                    },
                    'error': function() {

                        __log('validFriendRequest:error:');

                        $quiz.actions.showError(
                            $quiz.errors.on_load
                        );
                    }
                });
            }
        }
    };

    $quiz.actions.showLoader();

    $($quiz.elements.awards_action).click($quiz.actions.toggleAwards);

})(jQuery);


window.fbAsyncInit = function() {

    __log('fb.init');

    FB.init({
        appId: FB_APP_ID,
        cookie: true,
        xfbml: true,
        oauth: true
    });

    FB.Event.subscribe('auth.login', function(response) {
        __log('auth.login', this, arguments);
    });
    FB.Event.subscribe('auth.logout', function(response) {
        __log('auth.logout', this, arguments);
    });
    FB.Event.subscribe('edge.create', function(response) {
        __log('edge.create', this, arguments);
        // już lubisz, startuj!
        $quiz.actions.showBeginMessage();
    });

    FB.getLoginStatus(function(response) {

        __log('likes:getLoginStatus', arguments);

        if (response.authResponse) {
            $quiz.isAuth = true;

            __log('likes:getLoginStatus:auth', arguments);

            FB.api('/'+response.authResponse.userID+'/likes/193527090658231', function(response){

                __log('likes', arguments);

                if (response && response.data.length) {
                    __log('likes:length', arguments);
                    // lubi aplikację
                    $quiz.actions.showBeginMessage();
                }
                else
                {
                    __log('likes:length=0', arguments);
                    $quiz.actions.showHelloMessage();
                    // po 7sek pozwól grać!
                    setTimeout($quiz.actions.showBeginMessage, 7000);
//                    __log('likes:length=0:', arguments);
//                    // jeszcze nie lubi
//                    $quiz.actions.showHelloMessage();
                }


                
            });

        } else {
            __log('likes:getLoginStatus:not-auth', arguments);
            $quiz.isAuth = false;
            // no user session available, someone you dont know
            // niech polubi! - nie może byc blokady przez lika!
            // $quiz.actions.showHelloMessage();
            $quiz.actions.showBeginMessage();

            // po 7sek pozwól grać!
//            setTimeout($quiz.actions.showBeginMessage, 10000);

//            FB.login(function(response) {
//                if (response.authResponse) {
//
//                    FB.api('/'+response.authResponse.userID+'/likes/'+ FB_APP_ID, function(response){
//
//                        if (response.data.length) {
//                            __log('likes:length', arguments);
//                            // lubi aplikację
//                            $quiz.actions.showBeginMessage();
//                        } else {
//                            __log('likes:length=0:', arguments);
//                            // jeszcze nie lubi
//                            $quiz.actions.showHelloMessage();
//                        }
//
//                    });
//
//                } else {
//                    $quiz.actions.showHelloMessage();
//                }
//            }/*, {scope: 'email'}*/);
        }
    });
};


function sendRequestViaMultiFriendSelector() {
    FB.ui({method: 'apprequests',
        message: 'Wspaniały pomysł! quiz o małopolsce!'
    }, $quiz.actions.isValidFBFriendRequest);
}

function fb_login()
{
    $quiz.actions.showLoader();

    if ($quiz.isAuth) {
        $quiz.actions.loadQuestions();
    } else {
        FB.login(function(response) {
            if (response.authResponse) {
                $quiz.actions.loadQuestions();
                __log('Welcome!  Fetching your information.... ', response);
                FB.api('/me', function(response) {
                    __log('Good to see you:, ', response);
                });
            } else {
                $quiz.actions.showBeginMessage();
                __log('User cancelled login or did not fully authorize.', response);
            }
        }, {scope: 'email'});
    }
}
// email, offline_access

(function() {
    var e = document.createElement('script');
    e.async = true;
    e.src = document.location.protocol +
            '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
}());