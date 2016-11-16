<?php
/**
* class to handle traffic to yle api
*/

class yleApi {
  
  public $base_url = "https://external.api.yle.fi/v1/";
  public $image_base_url = "http://images.cdn.yle.fi/image/upload/";
  public $app_id = null;
  public $app_key = null;
  public $decrypt_key = null;
  // use file_get_contents
  public $easyFetch = true;
  
  // some cache variables to use
  public $cache = true;
  public $cache_path = "cache/";
  public $cache_time = 600; // 10 min

  /**
  * constructor
  */
  public function yleApi($app_id, $app_key, $decrypt_key) {
    $this->app_id = $app_id;
    $this->app_key = $app_key;
    $this->decrypt_key = $decrypt_key;
  }
  
  /**
  * build url
  */
  private function buildUrl($path, $params = array()) {
    $url = $this->base_url.$path."?";
    if(!empty($params)){
      foreach($params AS $k => $v){
        $url .= $k."=".urlencode($v)."&";
      }
    }
    $url .= "app_id=".$this->app_id;
    $url .= "&app_key=".$this->app_key;
    return $url;
  }
  
  /**
  * clean path
  * @param string $path
  */
  private function cleanUrl($path) {
    $path = substr($path, strlen($this->base_url));
    $path = substr($path, 0, strpos($path, "app_id"));
    $path = str_replace(array(".", "/", "&"), "", $path);
    return $path;
  }
  
  /**
  * fetch 
  * @param string $url
  */
  private function fetch($url) {
    $cpath = $this->cache_path."/".$this->cleanUrl($url);
    if($this->cache && file_exists($cpath) && (time() - filemtime($cpath)) < $this->cache_time){
      return file_get_contents($cpath);
    }
    if($this->easyFetch){
      $data = file_get_contents($url);
      if($this->cache){
        @file_put_contents($cpath, $data);
      }
      return $data;
    } else {
      // curl fetch
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_POST, 0);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      $data = curl_exec($curl);
      curl_close($curl);
      if($this->cache){
        @file_put_contents($cpath, $data);
      }
      return $data;
    }
  }
  
  /**
  * list categories 
  */
  public function categories() {
    $url = $this->buildUrl("programs/categories.json", array("scheme" => "areena-content-classification"));
    $c = $this->fetch($url);
    return json_decode($c);
  }
  

  /**
  * find programs
  * @param Array
  *   id - comma separated list of ids
  *   type - program|clip|tvcontent|tvprogram|tvclip|radiocontent|radioprogram|radioclip
  *   q - string to search
  *   mediaobject - audio|video
  *   category - comma separated list of categories fex 5-130 
  *   series - series_id (comma separated)
  *   availability - ondemand|future-ondemand|future-scheduled|in-future
  *   downloadable - if used true
  *   language - fi|sv
  *   region - fi|world
  *   order playcount.6h:asc|playcount.6h:desc|playcount.24h:asc|playcount.24h:desc|
  *         playcount.week:asc|playcount.week:desc|playcount.month:asc|playcount.month:desc|
  *         publication.starttime:asc|publication.starttime:desc|
  *         publication.endtime:asc|publication.endtime:desc|updated:asc|updated:desc
  *   limit
  *   offset
  */
  public function programs($params) {
    $url = $this->buildUrl("programs/items.json", $params);
    $c = $this->fetch($url);
    return json_decode($c);
  }
  
  /**
  * return program
  * @param string $id - program id
  */
  public function program($id) {
    $url = $this->buildUrl("programs/items/".$id.".json");
    $c = $this->fetch($url);
    return json_decode($c);
  }
  
  
  /**
  * report usage to yle. If you start playing, call this once
  * @param string $program_id
  * @param string $media_id
  */
  public function reportUsage($program_id, $media_id) {
    $url = $this->buildUrl("tracking/streamstart", array("program_id" => $program_id, "media_id" => $media_id));
    $c = $this->fetch($url);
  }
  
  /**
  * load media
  * @param string $program_id
  * @param string $media_id
  * @param string $type HLS|HDS
  */
  public function loadMedia($program_id, $media_id, $type = "HLS"){
    $url = $this->buildUrl("media/playouts.json", array("program_id" => $program_id, "media_id" => $media_id, "protocol" => $type));
    $c = $this->fetch($url);
    $data = json_decode($c);
    if(isset($data->data[0]->url)){
      $data->data[0]->url = $this->decodeUrl($data->data[0]->url);
    }
    return $data;
  }
  
  /**
  * returns url to image based on imageObject
  * @param string $transformations - (fex. "w_120,h_120,c_fit") http://cloudinary.com/documentation/image_transformations
  * @param string $format gif|png|jpg
  */
  public function imageUrl($image, $transformations, $format = "jpg"){
    $url = null;
    if(isset($image->id)) {
      $url = $this->image_base_url;
      if(!empty($transformations)){
        $url .= $transformations."/";
      }
      $url .= $image->id.".".$format;
    }
    return $url;
  }
  
  /**
  * decodeUrl - decodes coded url
  * @param string $url - base64 encrypted url
  */
  public function decodeUrl($url){
    $url = base64_decode($url);
    $iv = substr($url, 0,16);
    $message = substr($url, 16);
    $url = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->decrypt_key, $message, MCRYPT_MODE_CBC, $iv);
    return $url;
  }
  
}
