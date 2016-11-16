# phpYleApi

More about here
http://developer.yle.fi/api_docs.html

# usage #

```php
require("yleApi.php");
$api = new yleApi("app_id", "app_key", "decrypt_key");

//--- categories
$cats = $api->categories();
if(!empty($cats)){
  print_r($cats);
}

//--- programs
$params = array(
                "q" => "lapset", 
                "category" => "5-130", 
                "mediaobject" => "video", 
                "order" => "playcount.week:desc",
                "availability" => "ondemand",
                "contentprotection" => "22-0,22-1",
                "limit" => 5);

$programs = $api->programs($params);
foreach($programs->data AS $n => $prog){
  $id = $prog->id;
  print $prog->id."\n";
  print $prog->title->fi."\n";
  if(isset($prog->description->fi)){ print $prog->description->fi."\n"; }
  print $prog->duration."\n";
  //--- get the program info
  if(isset($prog->publicationEvent[0]->media->id)){
    $media = $prog->publicationEvent[0]->media;
    //--- print_r($media);
    $media_id = $media->id;
    $loaded = $api->loadMedia($id, $media_id);
    if(isset($loaded->data[0]->url)){
      //--- this is where the magic is
      print $loaded->data[0]->url."\n";
    }
    print '<img src="'.$api->imageUrl($prog->image, "w_120,h_120,c_fit").'" />';
    // print_r($loaded);
  }
  print "----------------\n";
}
//print_r($programs);

// use curl
$api->easyFetch = false;

// disable basic file cache
$api->cache = false;

// radiochannels
$services = $api->services("radiochannel");

// tvchannels
$services = $api->services("tvchannel");

// now playing on ylex
print_r($api->nowplaying("ylex"));

//--- get a single program info
$info = $api->program($id);

```

##Services##

###tvchannel###

| id                |   Nimi     |
----------------------------------
|yle-tv1			|Yle TV1     |
|yle-tv2			|Yle TV2     |
|yle-teema			|Yle Teema   |
|yle-fem			|Yle Fem     |
|tv-finland			|TV Finland  |
|yle-hd				|Yle HD      |

###radiochannel###

| id                           |   Nimi                     |
-------------------------------------------------------------
|yle-radio-1                   |Yle Radio 1                 |
|yle-puhe                      |Yle Puhe                    |
|yle-mondo                     |Yle Mondo                   |
|ylex                          |YleX                        |
|yle-x3m                       |Yle X3M                     |
|yle-radio-vega                |Yle Vega                    |
|radio-vega-huvudstadsregionen |Yle Vega Huvudstadsregionen |
|radio-vega-vastnyland         |Yle Vega Västnyland         |
|radio-vega-aboland            |Yle Vega Åboland            |
|radio-vega-osterbotten        |Yle Vega Österbotten        |
|radio-vega-ostnyland          |Yle Vega Östnyland          |
|yle-radio-suomi               |Yle Radio Suomi             |
|yle-radio-suomi-lappeenranta  |Yle Radio Suomi Lappeenranta|
|yle-radio-suomi-mikkeli       |Yle Radio Suomi Mikkeli     |
|yle-radio-suomi-kajaani       |Yle Radio Suomi Kajaani     |
|yle-radio-suomi-kotka         |Yle Radio Suomi Kotka       |
|yle-radio-suomi-lahti         |Yle Radio Suomi Lahti       |
|yle-radio-suomi-rovaniemi     |Yle Radio Suomi Rovaniemi   |
|yle-radio-suomi-oulu          |Yle Radio Suomi Oulu        |
|yle-radio-suomi-pohjanmaa     |Yle Radio Suomi Pohjanmaa   |
|yle-radio-suomi-joensuu       |Yle Radio Suomi Joensuu     |
|yle-radio-suomi-hameenlinna   |Yle Radio Suomi Hämeenlinna |
|yle-radio-suomi-kokkola       |Yle Radio Suomi Kokkola     |
|yle-radio-suomi-jyvaskyla     |Yle Radio Suomi Jyväskylä   |
|yle-radio-suomi-kemi          |Yle Radio Suomi Kemi        |
|yle-radio-suomi-kuopio        |Yle Radio Suomi Kuopio      |
|yle-radio-suomi-pori          |Yle Radio Suomi Pori        |
|yle-radio-suomi-tampere       |Yle Radio Suomi Tampere     |
|yle-radio-suomi-turku         |Yle Radio Suomi Turku       |
|yle-radio-suomi-helsinki      |Yle Radio Suomi Helsinki    |
|yle-sami-radio                |Yle Sámi Radio              |
|elavan-arkiston-nettiradio    |Elävän arkiston nettiradio  |

##Categories##

* 5-130 Tv
  * 5-131 Sarjat ja elokuvat
    * 5-133 Kotimaiset sarjat
    * 5-134 Ulkomaiset sarjat
    * 5-135 Elokuvat
    * 5-136 Komedia
    * 5-137 Jännitys
    * 5-138 Nuorten sarjat
  * 5-139 Viihde ja kulttuuri
    * 5-140 Viihde
    * 5-141 Kulttuuri
    * 5-142 Huumori
    * 5-143 Musiikki
    * 5-144 Pop, rock & jazz
    * 5-145 Viihdemusiikki
    * 5-146 Klassinen musiikki
    * 5-147 Konsertit ja tapahtumat
  * 5-148 Dokumentit ja fakta
    * 5-149 Dokumentit
    * 5-150 Fakta
    * 5-151 Ajankohtaisohjelmat
    * 5-152 Keskusteluohjelmat
    * 5-153 Asiaviihde
    * 5-154 Henkilökuvat
    * 5-155 Oppiminen
    * 5-156 Yhteiskunta
    * 5-157 Historia
    * 5-158 Luonto
    * 5-159 Tiede ja tekniikka
    * 5-160 Arki ja vapaa-aika
    * 5-161 Terveys ja hyvinvointi
  * 5-162 Uutiset
    * 5-163 Uutisohjelmat
  * 5-164 Urheilu
    * 5-166 Urheilumakasiini
    * 5-167 Alppihiihto
    * 5-168 Ampumahiihto
    * 5-169 Golf
    * 5-170 Hiihto
    * 5-171 Jääkiekko
    * 5-172 Jalkapallo
    * 5-173 Koripallo
    * 5-174 Käsipallo
    * 5-175 Lentopallo
    * 5-176 Lumilautailu
    * 5-177 Mäkihyppy
    * 5-178 Moottoriurheilu
    * 5-179 Nyrkkeily
    * 5-180 Paini
    * 5-181 Pesäpallo
    * 5-182 Pikaluistelu
    * 5-183 Purjehdus
    * 5-184 Pyöräily
    * 5-185 Ratsastus
    * 5-186 Raviurheilu
    * 5-187 Salibandy
    * 5-188 Suunnistus
    * 5-189 Taitoluistelu
    * 5-190 Tennis
    * 5-191 Uinti
    * 5-192 Voimalajit ja kamppailu
    * 5-193 Yhdistetty
    * 5-194 Yleisurheilu
  * 5-195 Lapset
    * 5-197 Animaatiot
    * 5-198 Sadut
    * 5-199 Lasten elokuvat
* 5-200 Radio
  * 5-201 Musiikki ja viihde
    * 5-202 Musiikki
    * 5-203 Viihde
    * 5-204 Huumori
    * 5-205 Populaarimusiikki
    * 5-206 Jazz & etno
    * 5-207 Iskelmä & Nostalgia
    * 5-209 Klassinen musiikki
    * 5-210 Haastattelut
  * 5-212 Äänikirjat ja kuunnelmat
    * 5-214 Äänikirjat
    * 5-215 Kuunnelmat
  * 5-218 Fakta ja Kulttuuri
    * 5-217 Kulttuuri
    * 5-220 Luonto
    * 5-221 Tiede
    * 5-222 Historia
    * 5-223 Arki ja vapaa-aika
    * 5-224 Terveys ja hyvinvointi
    * 5-225 Hartausohjelmat
  * 5-226 Uutiset
    * 5-227 Uutisohjelmat
  * 5-228 Urheilu
    * 5-230 Alppihiihto
    * 5-231 Ampumahiihto
    * 5-232 Golf
    * 5-233 Hiihto
    * 5-234 Jääkiekko
    * 5-235 Jalkapallo
    * 5-236 Koripallo
    * 5-237 Käsipallo
    * 5-238 Lentopallo
    