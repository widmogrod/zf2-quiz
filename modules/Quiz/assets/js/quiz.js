(function($){

    var $data = {
        'questions': [
            {
                'id': 1,
                'title': 'JAKI ZAMEK WIDAĆ NA FILMIE?',
                'content': 'http://www.youtube.com/watch?v=6cv9H0m0v3o',
                'type': 'video',
                'answers': [
                    {
                        'answer': 'Wawel',
                        'id': 1
                    },
                    {
                        'answer': 'Zamek w Nowym Sączu',
                        'id': 2
                    },
                    {
                        'answer': 'Zamek w Targu Nowotarskim',
                        'id': 3
                    }
                ]
            },
            {
                'id': 2,
                'title': 'Co to za dzwięk?',
                'content': 'http://www.youtube.com/watch?v=6cv9H0m0v3o',
                'type': 'video',
                'answers': [
                    {
                        'answer': 'Hejnał mariacki',
                        'id': 1
                    },
                    {
                        'answer': 'Marsz dąbrowskiego',
                        'id': 2
                    },
                    {
                        'answer': 'Mrrr mtrr',
                        'id': 3
                    }
                ]
            },
            {
                'id': 3,
                'title': 'JAKI ZAMEK WIDAĆ NA FILMIE?',
                'content': 'http://www.youtube.com/watch?v=6cv9H0m0v3o',
                'type': 'video',
                'answers': [
                    {
                        'answer': 'Wawel',
                        'id': 1
                    },
                    {
                        'answer': 'Zamek w Nowym Sączu',
                        'id': 2
                    },
                    {
                        'answer': 'Zamek w Targu Nowotarskim',
                        'id': 3
                    }
                ]
            },
            {
                'id': 4,
                'title': 'Co to za dzwięk?',
                'content': 'http://www.youtube.com/watch?v=6cv9H0m0v3o',
                'type': 'video',
                'answers': [
                    {
                        'answer': 'Hejnał mariacki',
                        'id': 1
                    },
                    {
                        'answer': 'Marsz dąbrowskiego',
                        'id': 2
                    },
                    {
                        'answer': 'Mrrr mtrr',
                        'id': 3
                    }
                ]
            },
            {
                'id': 5,
                'title': 'JAKI ZAMEK WIDAĆ NA FILMIE?',
                'content': 'http://www.youtube.com/watch?v=6cv9H0m0v3o',
                'type': 'video',
                'answers': [
                    {
                        'answer': 'Wawel',
                        'id': 1
                    },
                    {
                        'answer': 'Zamek w Nowym Sączu',
                        'id': 2
                    },
                    {
                        'answer': 'Zamek w Targu Nowotarskim',
                        'id': 3
                    }
                ]
            },
            {
                'id': 6,
                'title': 'Co to za dzwięk?',
                'content': 'http://www.youtube.com/watch?v=6cv9H0m0v3o',
                'type': 'video',
                'answers': [
                    {
                        'answer': 'Hejnał mariacki',
                        'id': 1
                    },
                    {
                        'answer': 'Marsz dąbrowskiego',
                        'id': 2
                    },
                    {
                        'answer': 'Mrrr mtrr',
                        'id': 3
                    }
                ]
            }
        ]
    };

    function __log()
    {
        if (console && console.log) {
            console.log.apply(console, arguments);
        }
    }

    var $quiz = {
        'options': {
            'question_timeout': 8,
            'url': {
                'quiz_data': '/app/getquiz'
            }
        },

        'errors': {
            'on_load': 'Wystąpił problem z w czasie ładowania danych. Proszę <a onclick="window.location.refresh();">odświeżyć</a> stronę ponownie.'
        },

        'elements': {
            'question': '#question',
            'question_no': '#question-no',
            'question_time': '#question-time',
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
            },
            'nextQuestion':function() {

                if (!$quiz.questionsData)
                {
                    __log('$quiz.questionsData:', $quiz.questionsData);

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
                            try {
                                $quiz.questionsData = data.questions;
                                $quiz.actions.nextQuestion();
                            } catch (e) {
                                __log(e);
                            }
                        }
                    });
                }

                $quiz.actions.nextQuestion();
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
                    case 'video':
                        var videoId = question.content.match(/v=([^?&]+)/)[1];
                        q = '<iframe width="340" height="203" src="http://www.youtube.com/embed/'+ videoId +'?autoplay=1" frameborder="0" allowfullscreen></iframe>';
                        break;
                }

                var answers = [];

                answers.push('<ul class="answers">');
                $(question.answers).each(function(k, item) {
                    answers.push('<li answerId="'+ item.id +'">'+ item.answer +'</li>');
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
            'addAnswerForQuestion': function(answerId, questionId, time) {
                $quiz.answersCollection[questionId] = {
                    'questionId': questionId,
                    'answerId': answerId,
                    'time': time
                };
            },
            'showError': function(errorMessage) {
                $quiz.actions.hideLoader();
                $($quiz.elements.error_container).html(errorMessage).show();
            }
        }
    };

    $quiz.actions.showLoader();
    $quiz.actions.loadQuestions();

})(jQuery);