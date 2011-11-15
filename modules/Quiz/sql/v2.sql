-- ALTER TABLE quiz_user ALTER COLUMN fullname TYPE character varying(250);
ALTER TABLE quiz_user ADD COLUMN fullname character varying(250);
ALTER TABLE quiz_user ADD COLUMN facebookId bigint;

CREATE UNIQUE INDEX idx__facebook_id__quiz_user
   ON quiz_user (facebookid ASC NULLS LAST);

ALTER TABLE quiz_quiz ADD COLUMN isclose boolean;


