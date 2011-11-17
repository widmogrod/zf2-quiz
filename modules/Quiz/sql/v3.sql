CREATE SEQUENCE quiz_friend_invite_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE quiz_friend_invite (id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, userId INT NOT NULL, PRIMARY KEY(id));
ALTER TABLE quiz_quiz ALTER user_id SET NOT NULL;
ALTER TABLE quiz_quiz ALTER isclose SET NOT NULL;
ALTER TABLE quiz_quiz_answer ALTER quiz_id SET NOT NULL;
ALTER TABLE quiz_quiz_answer ALTER answer_id SET NOT NULL;
ALTER TABLE quiz_user DROP avatar;