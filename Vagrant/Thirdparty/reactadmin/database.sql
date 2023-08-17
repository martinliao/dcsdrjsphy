#
# TABLE STRUCTURE FOR: karyawan
#

DROP TABLE IF EXISTS `karyawan`;

CREATE TABLE `karyawan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(255) NOT NULL,
  `name` varchar(30) NOT NULL,
  `phone_number` char(15) NOT NULL,
  `gender` enum('Laki-Laki','Perempuan') NOT NULL,
  `email` varchar(50) NOT NULL,
  `religion` enum('Islam','Kristen','Budha','Hindu') NOT NULL,
  `pendidikan` enum('SD','SMP','SMA','Diploma 3') NOT NULL,
  `address` text NOT NULL,
  `id_pekerjaan` int(11) NOT NULL,
  `stat` enum('Karyawan','Kontributor') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (12, 'KCS-01', 'Ayatullah', '083929239192', 'Laki-Laki', 'ayatullah@gmail.com', 'Islam', 'SMA', 'Bogor', 3, 'Karyawan');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (13, 'KCS-02', 'Edwin Suwandana', '087827677855', 'Laki-Laki', 'edwin@ceklissatu.com', 'Islam', 'SMA', 'Bogor', 4, 'Karyawan');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (14, 'KCS-03', 'Agung Prabowo', '081283003336', 'Laki-Laki', 'agungprabowo@ceklissatu.com', 'Islam', 'SMA', 'Bogor', 5, 'Karyawan');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (15, 'KCS-04', 'Andry Setyoso', '083819294392', 'Laki-Laki', 'andrysetyoso@ceklissatu.com', 'Islam', 'SMA', 'Bogor', 5, 'Karyawan');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (16, 'KCS-05', 'Ahmad Reda', '08989595394', 'Laki-Laki', 'ahmadreda@ceklissatu.com', 'Islam', 'SMA', 'Bogor', 6, 'Karyawan');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (17, 'KCS-06', 'Chaeron Syah', '081293357656', 'Laki-Laki', 'chaeronsyah@ceklissatu.com', 'Islam', 'SMA', 'Cianjur', 2, 'Kontributor');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (18, 'KCS-07', 'Cucu Agus Lesamana', '0881239394322', 'Laki-Laki', 'cucuagus@ceklissatu.com', 'Islam', 'SMA', 'Banjar', 2, 'Kontributor');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (19, 'KCS-08', 'Ramdhan TB', '083832899112', 'Laki-Laki', 'ramdhan@ceklissatu.com', 'Islam', 'SMA', 'Bogor', 2, 'Kontributor');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (20, 'KCS-09', 'Andi Muksin Adiwijaya', '084712737123', 'Laki-Laki', 'andi@ceklissatu.com', 'Islam', 'SMA', 'Sumbar', 2, 'Kontributor');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (21, 'KCS-10', 'Johnit', '0823818318223', 'Laki-Laki', 'johnit@ceklissatu.com', 'Islam', 'SMA', 'Jakarta Barat', 2, 'Karyawan');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (22, 'KCS-11', 'Mulyana', '083183812398', 'Laki-Laki', 'mulyana@ceklissatu.com', 'Islam', 'SMA', 'Bogor', 2, 'Kontributor');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (23, 'KCS-12', 'Sudrajat', '0838182393921', 'Laki-Laki', 'sudrajat@ceklissatu.com', 'Islam', 'SMA', 'Banten', 2, 'Kontributor');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (24, 'KCS-13', 'Riffan Fauzan', '08384779192', 'Laki-Laki', 'fauzan@ceklissatu.com', 'Islam', 'SMA', 'Kabupaten Bogor', 2, 'Kontributor');
INSERT INTO `karyawan` (`id`, `nik`, `name`, `phone_number`, `gender`, `email`, `religion`, `pendidikan`, `address`, `id_pekerjaan`, `stat`) VALUES (25, 'KCS-14', 'Dwi Susanto', '08382832372', 'Laki-Laki', 'dwisusanto@ceklissatu.com', 'Islam', 'SMA', 'Bogor', 7, 'Karyawan');


#
# TABLE STRUCTURE FOR: medsos
#

DROP TABLE IF EXISTS `medsos`;

CREATE TABLE `medsos` (
  `id_medsos` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) NOT NULL,
  `warna` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id_medsos`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO `medsos` (`id_medsos`, `icon`, `warna`, `link`) VALUES (1, 'fa fa-fw fa-facebook', 'btn-primary', '#');
INSERT INTO `medsos` (`id_medsos`, `icon`, `warna`, `link`) VALUES (2, 'fa fa-fw fa-instagram', 'btn-warning', '#');
INSERT INTO `medsos` (`id_medsos`, `icon`, `warna`, `link`) VALUES (3, 'fa fa-fw fa-youtube-play', 'btn-danger', '#');
INSERT INTO `medsos` (`id_medsos`, `icon`, `warna`, `link`) VALUES (5, 'fa fa-fw fa-twitter', 'btn-info', '#');


#
# TABLE STRUCTURE FOR: payroll
#

DROP TABLE IF EXISTS `payroll`;

CREATE TABLE `payroll` (
  `id_gaji` int(12) NOT NULL AUTO_INCREMENT,
  `id_karyawan` int(12) NOT NULL,
  `jmlh_berita` varchar(255) NOT NULL,
  `jmlh_video` varchar(255) NOT NULL,
  `tanggal` varchar(25) NOT NULL,
  PRIMARY KEY (`id_gaji`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

INSERT INTO `payroll` (`id_gaji`, `id_karyawan`, `jmlh_berita`, `jmlh_video`, `tanggal`) VALUES (13, 12, '0', '0', '2020-04-16');
INSERT INTO `payroll` (`id_gaji`, `id_karyawan`, `jmlh_berita`, `jmlh_video`, `tanggal`) VALUES (14, 13, '0', '0', '2020-04-20');


#
# TABLE STRUCTURE FOR: pekerjaan
#

DROP TABLE IF EXISTS `pekerjaan`;

CREATE TABLE `pekerjaan` (
  `id_pekerjaan` int(11) NOT NULL AUTO_INCREMENT,
  `jabatan` varchar(24) NOT NULL,
  `gapok` int(11) NOT NULL,
  `tukes` int(11) NOT NULL,
  `tutra` int(11) NOT NULL,
  `honor_1` int(11) NOT NULL,
  `honor_2` int(11) NOT NULL,
  `pph` varchar(255) NOT NULL,
  `bpjs` varchar(255) NOT NULL,
  PRIMARY KEY (`id_pekerjaan`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

INSERT INTO `pekerjaan` (`id_pekerjaan`, `jabatan`, `gapok`, `tukes`, `tutra`, `honor_1`, `honor_2`, `pph`, `bpjs`) VALUES (2, 'Wartawan', 0, 0, 0, 10000, 20000, '0', '0');
INSERT INTO `pekerjaan` (`id_pekerjaan`, `jabatan`, `gapok`, `tukes`, `tutra`, `honor_1`, `honor_2`, `pph`, `bpjs`) VALUES (3, 'Redaktur', 3500000, 0, 0, 0, 0, '0', '0');
INSERT INTO `pekerjaan` (`id_pekerjaan`, `jabatan`, `gapok`, `tukes`, `tutra`, `honor_1`, `honor_2`, `pph`, `bpjs`) VALUES (4, 'Redaktur Pelaksana', 5000000, 0, 0, 0, 0, '0', '0');
INSERT INTO `pekerjaan` (`id_pekerjaan`, `jabatan`, `gapok`, `tukes`, `tutra`, `honor_1`, `honor_2`, `pph`, `bpjs`) VALUES (5, 'IT Support', 4500000, 0, 0, 0, 0, '0', '0');
INSERT INTO `pekerjaan` (`id_pekerjaan`, `jabatan`, `gapok`, `tukes`, `tutra`, `honor_1`, `honor_2`, `pph`, `bpjs`) VALUES (6, 'Staff', 2200000, 0, 0, 0, 0, '0', '0');
INSERT INTO `pekerjaan` (`id_pekerjaan`, `jabatan`, `gapok`, `tukes`, `tutra`, `honor_1`, `honor_2`, `pph`, `bpjs`) VALUES (7, 'Photographer', 1500000, 0, 0, 0, 0, '0', '0');


#
# TABLE STRUCTURE FOR: statistics
#

DROP TABLE IF EXISTS `statistics`;

CREATE TABLE `statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url_id` (`url_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# TABLE STRUCTURE FOR: system_icon
#

DROP TABLE IF EXISTS `system_icon`;

CREATE TABLE `system_icon` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `icon_name` varchar(60) NOT NULL,
  `icon` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (1, 'f085', 'fa fa-fw fa fa-cogs');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (2, 'f0e4', 'fa fa-fw fa fa-tachometer');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (3, 'f1c0', 'fa fa-fw fa fa-database');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (4, 'f26e', 'fa fa-fw fa fa-500px');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (5, 'f270', 'fa fa-fw fa fa-amazon');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (6, 'f24e', 'fa fa-fw fa fa-balance-scale');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (7, 'f274', 'fa fa-fw fa fa-calendar-check-o');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (8, 'f272', 'fa fa-fw fa fa-calendar-minus-o');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (9, 'f271', 'fa fa-fw fa fa-calendar-plus-o');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (10, 'f0c5', 'fa fa-fw fa fa-clone');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (11, 'f268', 'fa fa-fw fa fa-chrome');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (12, 'f269', 'fa fa-fw fa fa-firefox');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (13, 'f280', 'fa fa-fw fa fa-fonticons');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (14, 'f265', 'fa fa-fw fa fa-get-pocket');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (15, 'f055', 'fa fa-fw fa fa-plus-circle');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (16, 'f007', 'fa fa-fw fa fa-user');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (17, 'f234', 'fa fa-fw fa fa-user-plus');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (18, 'f21b', 'fa fa-fw fa fa-user-secret');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (19, 'f235', 'fa fa-fw fa fa-user-times');
INSERT INTO `system_icon` (`id`, `icon_name`, `icon`) VALUES (20, 'f0c0', 'fa fa-fw fa fa-users');


#
# TABLE STRUCTURE FOR: system_settings
#

DROP TABLE IF EXISTS `system_settings`;

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  `nohp` varchar(15) NOT NULL,
  `alamat` text NOT NULL,
  `logo` varchar(255) NOT NULL,
  `footer_right` varchar(244) NOT NULL,
  `footer_left` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `system_settings` (`id`, `nama`, `nohp`, `alamat`, `logo`, `footer_right`, `footer_left`) VALUES (1, 'Reactmores', '083819454642', 'Lorem Ipsum Dolor', 'noimage1.png', 'Version 1,0', 'Copyright © 2020 Reactmore - All Rights Reserved.');


#
# TABLE STRUCTURE FOR: urls
#

DROP TABLE IF EXISTS `urls`;

CREATE TABLE `urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `urls` (`id`, `url`, `alias`, `created`) VALUES (1, 'https://reactmore.com', '5ea618ba9f803', '2020-04-27 01:26:50');

#
# TABLE STRUCTURE FOR: user_role
#

DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO `user_role` (`id_role`, `role`) VALUES (1, 'Admin');
INSERT INTO `user_role` (`id_role`, `role`) VALUES (5, 'Demo');

#
# TABLE STRUCTURE FOR: user
#

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `is_active` int(11) NOT NULL,
  PRIMARY KEY (`id_user`),
  KEY `fk_role` (`id_role`),
  CONSTRAINT `fk_role` FOREIGN KEY (`id_role`) REFERENCES `user_role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `user` (`id_user`, `username`, `password`, `id_role`, `is_active`) VALUES (1, 'Admin', '$2y$10$4IPLOB4CrQkgNOhDGDeIc.yUYLpnCypmegplvsQKa.RoMgRQhVD9e', 1, 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `id_role`, `is_active`) VALUES (3, 'Demo', '$2y$10$4IPLOB4CrQkgNOhDGDeIc.yUYLpnCypmegplvsQKa.RoMgRQhVD9e', 5, 1);


#
# TABLE STRUCTURE FOR: user_menu
#

DROP TABLE IF EXISTS `user_menu`;

CREATE TABLE `user_menu` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) NOT NULL,
  `icon` varchar(30) NOT NULL,
  `is_active` int(11) NOT NULL,
  `no_order` int(11) NOT NULL,
  PRIMARY KEY (`id_menu`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

INSERT INTO `user_menu` (`id_menu`, `title`, `icon`, `is_active`, `no_order`) VALUES (1, 'Admin Menu', 'fa fa-laptop', 1, 2);
INSERT INTO `user_menu` (`id_menu`, `title`, `icon`, `is_active`, `no_order`) VALUES (6, 'Dashboard', 'fa fa-fw fa fa-tachometer', 1, 1);
INSERT INTO `user_menu` (`id_menu`, `title`, `icon`, `is_active`, `no_order`) VALUES (9, 'Settings', 'fa fa-fw fa fa-cogs', 1, 3);
INSERT INTO `user_menu` (`id_menu`, `title`, `icon`, `is_active`, `no_order`) VALUES (10, 'Master Data', 'fa fa-fw fa fa-database', 1, 4);
INSERT INTO `user_menu` (`id_menu`, `title`, `icon`, `is_active`, `no_order`) VALUES (11, 'Shortener Link', 'fa fa-fw fa fa-calendar-plus-o', 1, 5);


#
# TABLE STRUCTURE FOR: user_access
#

DROP TABLE IF EXISTS `user_access`;

CREATE TABLE `user_access` (
  `id_access` int(11) NOT NULL AUTO_INCREMENT,
  `id_menu` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  PRIMARY KEY (`id_access`),
  KEY `fk_a_role` (`id_role`),
  KEY `fk_a_menu` (`id_menu`),
  CONSTRAINT `fk_a_menu` FOREIGN KEY (`id_menu`) REFERENCES `user_menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_a_role` FOREIGN KEY (`id_role`) REFERENCES `user_role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=latin1;

INSERT INTO `user_access` (`id_access`, `id_menu`, `id_role`) VALUES (1, 1, 1);
INSERT INTO `user_access` (`id_access`, `id_menu`, `id_role`) VALUES (67, 6, 1);
INSERT INTO `user_access` (`id_access`, `id_menu`, `id_role`) VALUES (71, 9, 1);
INSERT INTO `user_access` (`id_access`, `id_menu`, `id_role`) VALUES (75, 10, 1);
INSERT INTO `user_access` (`id_access`, `id_menu`, `id_role`) VALUES (77, 11, 1);


#
# TABLE STRUCTURE FOR: user_submenu
#

DROP TABLE IF EXISTS `user_submenu`;

CREATE TABLE `user_submenu` (
  `id_submenu` int(11) NOT NULL AUTO_INCREMENT,
  `id_menu` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `icon` varchar(30) NOT NULL,
  `url` varchar(30) NOT NULL,
  `is_active` int(11) NOT NULL,
  `no_urut` int(11) NOT NULL,
  PRIMARY KEY (`id_submenu`),
  KEY `fk_menu` (`id_menu`),
  CONSTRAINT `fk_menu` FOREIGN KEY (`id_menu`) REFERENCES `user_menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;

INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (1, 1, 'User Management', 'fa fa-fw fa-users', 'user', 1, 2);
INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (2, 1, 'Role management', 'fa fa-fw fa-cogs', 'role', 1, 1);
INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (3, 1, 'Menu Management', 'fa fa-fw fa-code', 'menu', 1, 3);
INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (6, 1, 'Access Management', 'fa fa-fw fa-lock', 'access', 1, 4);
INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (12, 6, 'Dashboard', 'fa fa-fw fa-tachometer', 'admin/dashboard', 1, 1);
INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (38, 9, 'Site Setting', 'fa fa-fw fa-map', 'settings', 1, 1);
INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (42, 10, 'Data Divisi', 'fa fa-folder', 'divisi', 1, 2);
INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (44, 9, 'Backup &amp; Restore', 'fa fa-database', 'database', 1, 2);
INSERT INTO `user_submenu` (`id_submenu`, `id_menu`, `title`, `icon`, `url`, `is_active`, `no_urut`) VALUES (45, 11, 'Short Link', 'fa fa-tachometer', 'url', 1, 1);


