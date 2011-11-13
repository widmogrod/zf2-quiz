(function($){

    var $data = {
        'questions': [
            {
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

    var $quiz = {
        'options': {
            'question_timeout': 20
        },
        'elements': {
            'question': '#question',
            'question_no': '#question-no',
            'question_time': '#question-time',
            'question_title': '#question_title',
            'answers': '#answers',
            'loader': '#loader'
        },
        'question_no': 0,
        'timer': 0,
        'timeInterval': null,
        'questionPosition': -1,
        'questionsData': null,
        'actions': {
            'nextQuestion':function() {
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
                console.log('questionEnds', $quiz);
                $quiz.actions.stopTimer();
            },
            'loadQuestions': function() {
                if (null === $quiz.questionsData) {
                    $quiz.questionsData = $data.questions;
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

                if (currentTime < 0) {
                    $quiz.actions.nextQuestion();
                } else {
                    $($quiz.elements.question_time).text(--currentTime);
                }
            },
            'setupQuestion': function(question) {

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

                $quiz.actions.hideLoader();

                $quiz.actions.startTimer();
            }
        }
    };

    $quiz.actions.showLoader();
    $quiz.actions.loadQuestions();

})(jQuery);