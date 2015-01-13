-- SQL schema for HWRatings

CREATE TABLE `hw_ratings` (
  hw_user_id int unsigned NOT NULL,
  hw_page_id int unsigned NOT NULL,
  hw_rating tinyint NOT NULL,
  hw_timestamp CHAR(14) NOT NULL,
  hw_deleted BOOL DEFAULT false,
  primary key (hw_user_id, hw_page_id)
);

CREATE INDEX hw_page_primary ON hw_ratings ( hw_page_id );
