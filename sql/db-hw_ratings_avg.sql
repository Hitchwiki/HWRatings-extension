-- SQL schema for HWRatings averages

CREATE TABLE `hw_ratings_avg` (
  hw_page_id int unsigned PRIMARY KEY NOT NULL,
  hw_average_rating tinyint NOT NULL
);

CREATE INDEX hw_page_primary ON hw_ratings_avg ( hw_page_id );
