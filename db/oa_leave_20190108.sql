ALTER TABLE `oa_leave` ADD COLUMN `country` enum('china','philippines','other') NULL DEFAULT NULL AFTER `type`;

ALTER TABLE `oa_leave` ADD COLUMN `secondReviewer` char(30) NULL AFTER `reviewedDate`,ADD COLUMN `secondReviewerDate` datetime(0) NULL AFTER `secondReviewer`;
