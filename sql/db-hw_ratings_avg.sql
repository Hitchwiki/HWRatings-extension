-- SQL schema for HWRatings averages

CREATE TABLE `hw_ratings_avg` (
  hw_page_id int unsigned PRIMARY KEY NOT NULL,
  hw_count_rating int unsigned NOT NULL,
  hw_average_rating tinyint NOT NULL,
  hw_deleted BOOL DEFAULT false
);
