-- 创建users表
create table `users` (`id` int auto_increment not null, `user_name` varchar(40) unique not null, `password` varchar(255) not null, `token` varchar(100) unique, `reg_time` datetime default now() not null, primary key (`id`));
-- 创建 blog 表
create table `blog` (`id` int auto_increment not null, `user_id` int not null, `content` text not null, `title` varchar(255) not null, `release_time` datetime default now() not null, `is_delete` int default 1, `delete_time` datetime, `update_time` datetime, primary key (`id`));
-- 创建comment表
create table `comment` (`id` int auto_increment not null, `blog_id` int not null, `user_id` int not null, `content` varchar(255) not null, `release_time` datetime default now() not null, `is_delete` int default 1, `delete_time` datetime, primary key (`id`));