ALTER TABLE quiz_quiz_answer ALTER answer_id SET NOT NULL;
ALTER TABLE quiz_user ADD email VARCHAR(255) DEFAULT NULL;
ALTER TABLE quiz_user DROP username;