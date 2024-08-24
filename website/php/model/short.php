<?php
class Shortened {
    public string $shortcode;
    public string $shortenedUrl;
    public string $url;

    public int $created;
    public ?int $expiry;
    public bool $includeInSearch;
    private DateTimeZone $timezone;

    /**@throws Exception*/
    public function __construct(string $shortcode, string $shortenedUrl, string $url, int $expiry=null, int $created=null, bool $includeInSearch=true, string $usr_timezone = 'Europe/Warsaw') {
        $this->shortcode = $shortcode;
        $this->shortenedUrl = $shortenedUrl;
        $this->url = $url;
        $this->expiry = $expiry;
        $this->includeInSearch = $includeInSearch;
        if (isset($created))
            $this->created = $created;
        else
            $this->created = time();
        $this->timezone = new DateTimeZone($usr_timezone);
    }
    /**@throws Exception*/
    public function getExpiryFormatted(string $dateformat='d-m-Y H:i:s'): string {
        $result = "never";
        if ($this->expiry != null) {
            $datetime = new DateTime('@' . $this->expiry);
            $datetime->setTimezone($this->timezone);
            $result = $datetime->format($dateformat);
        }
        return $result;
    }

    /**@throws Exception*/
    public function getCreationDate(string $dateformat='d-m-Y H:i:s'): string {
        $datetime = new DateTime('@' . $this->created);
        $datetime->setTimezone($this->timezone);
        return $datetime->format($dateformat);
    }
}
