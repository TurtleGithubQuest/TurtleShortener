<?php
class Shortened {
    public string $shortcode;
    public string $shortenedUrl;
    public string $url;
    public ?int $expiry;
    public function __construct(string $shortcode, string $shortenedUrl, string $url, int $expiry=null) {
        $this->shortcode = $shortcode;
        $this->shortenedUrl = $shortenedUrl;
        $this->url = $url;
        $this->expiry = $expiry;
    }
    public function getExpiryFormatted(string $dateformat='m-d-Y H:i:s'): string {
        $result = "never";
        if ($this->expiry != null)
            $result = date($dateformat, $this->expiry);
        return $result;
    }
}