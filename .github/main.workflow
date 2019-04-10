workflow "Test the Toggle package" {
  on = "push"
  resolves = [
    "Test"
  ]
}

action "Install" {
  uses = "docker://104corp/php-testing:7.3"
  args = "composer install"
}

action "Check coding style" {
  needs = "Install"
  uses = "docker://php:7.3"
  args = "php vendor/bin/phpcs"
}

action "Test" {
  needs = "Check coding style"
  uses = "docker://php:7.3"
  args = "php vendor/bin/phpunit"
}
