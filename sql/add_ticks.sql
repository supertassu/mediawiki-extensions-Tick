--
-- Tables for Tick extension
--

begin;

-- Ticks table
create table /*_*/tick (
    tick_id int unsigned not null primary key auto_increment,
    -- Key to page.page_id.
    tick_page int unsigned not null,
    tick_name varchar(128) not null,
    tick_last_ticked timestamp not null default current_timestamp
) /*$wgDBTableOptions*/;

-- For querying all notes on a certain page
create index /*i*/tick_page ON /*_*/tick (tick_page);

create unique index /*i*/tick_name_page_unique on tick (tick_page, tick_name);

commit;
