<?php
function getLanguage($userLang) {
    if (file_exists("php/lang/{$userLang}.php")) {
        return $userLang;
    } else {
        return $userLang == "sk" ? "cz" : "en";
    }
}
function loadLanguage(): void {
    global$user_language;
    $user_language = $_GET["lang"] ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $user_language = getLanguage($user_language);
    include_once("php/lang/".$user_language.".php");
}
function validateTranslation($baseTranslation, $otherTranslation) {
  $missingEntries = array_diff_key($baseTranslation, $otherTranslation);

  if (empty($missingEntries)) {
    return true;  // All entries are present in the other translation
  }
  else {
    return $missingEntries;  // Return keys that are missing in the other translation
  }
}