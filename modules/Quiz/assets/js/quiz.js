var $quiz;

(function($){

    function __log()
    {
        if (console && console.log) {
            console.log.apply(console, arguments);
        }
    }

    var Helper =
    {
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
                'quiz_data': '/app/getquiz',
                'quiz_finish': '/app/results'
            }
        },

        'errors': {
            'on_load': 'Wystąpił problem z w czasie ładowania danych. Proszę <a onclick="window.location.refresh();">odświeżyć</a> stronę ponownie.',
            'on_end': 'Wystąpił problem z w czasie ładowania danych. Proszę <a onclick="window.location.refresh();">odświeżyć</a> stronę ponownie.'
        },

        'elements': {
            'results': '#question',
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

        'actions':
        {
            'clearAll': function(){
                $quiz.action.stopTimer();

                $quiz.question_no = 0,
                $quiz.timer = 0,
                $quiz.timeInterval = null,
                $quiz.questionPosition = -1,
                $quiz.questionsData = null,
                $quiz.answersCollection = {},
                $quiz.currentQuestionId = null;
                $quiz.quizId = null;
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
                $quiz.actions.stopTimer();
                $quiz.actions.clearCanvas();
                $quiz.actions.hideControls();

                $($quiz.elements.question_title).text('Wyniki');

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
                    'timeout': 10*1000,
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
                if (null === $quiz.questionsData)
                {
                    $quiz.actions.showLoader();

                    $.ajax({
                        'url' : $quiz.options.url.quiz_data,
                        'dataType': 'json',
                        'timeout': 10*1000,
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
                        q = '<img src="/upload/'+ question.content+ '" width="340" >';
                        break;

                    case 'text':
                        q = '<p>'+ question.content+ '</p>';
                        break;
                }

                var answers = [];

                answers.push('<ul class="answers">');
                $(question.answers).each(function(k, item) {
                    answers.push('<li answerId="'+ item.id +'">'+ item.name +'</li>');
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
                $($quiz.elements.error_container).html(errorMessage).show();
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
            'renderResult': function(result) {
                var results = [];

                results.push('<ul class="results_list">');
                $(result).each(function(k, item) {
                    results.push('<li><img src="https://graph.facebook.com/'+ item.username +'/picture"><span class="name"> '+ item.fullname +'</span><span class="points"> '+ item.points +'</span></li>');
                });
                results.push('</ul>');

                $($quiz.elements.results).html(results.join("\n"));
            }
        }
    };

})(jQuery);