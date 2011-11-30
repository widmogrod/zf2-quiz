ALTER TABLE quiz_question ADD isActive BOOLEAN;
ALTER TABLE quiz_quiz ALTER user_id DROP NOT NULL;
ALTER TABLE quiz_quiz_answer ALTER quiz_id DROP NOT NULL;
ALTER TABLE quiz_quiz_answer ALTER answer_id DROP NOT NULL;

UPDATE quiz_question SET isActive = true;