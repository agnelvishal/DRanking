use condense;
#show tables;
#desc `symba.io`;

#select * from `yourstory.com` where date between "2018-01-14" and "2018-01-16" order by fbshares desc limit 100 ;
select * from `yourstory.com` where url like '%google%' ;


#CREATE INDEX dateI ON `yourstory.com` (date);
#drop index fb on `yourstory.com` ;
#show index from `yourstory.com`;

#select * from `yourstory.com` where title like '%surat%' ;
#SELECT * FROM `skcript.com`;
#drop table `symba.io` ;
#select * from `yourstory.com` ;

#select * from `symba.io` ;


#desc `yourstory.com`;

#SELECT * FROM `yourstory.com` where url='https://yourstory.com/mystory/f72cfb1102-how-are-mobiles-apps-changing-the-world-'

#update `techcrunch.com` set image = replace(image,SUBSTRING_INDEX(image,'?w=',-1),320) where image like '%?w=%';