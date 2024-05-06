    # e-commerce Shop B2b demo of create Order module


   * [Technologies](#technologies)
   * [Setup](#setup)

**Technologies**
  <ul>
    <li>PHP 8.2, Symfony 7</li>
    <li>Docker, Docker Compose</li>
    <li>Debian 10/11 with Makefile</li>
    <li>Makefile for Windows 10/11(current is in testing)</li>
    <li>Mysql, Nginx, Redis</li>
  </ul>

   ## 01. Initial project setup

  01. [x] Implement environment on Docker for run Demo

  ### Content of specification:

  <b><i>Environments and build targets</i></b>

 Celem jest przygotowanie i konfiguracja, w której
można używać kontenerów do:
  <ul>
      <li>local development</li>
      <li>CI/CD pipeline</li>
      <li>stage preproduction and production</li>
  </ul>
 i mimo dążenia do zapewnienia parytetu między tymi różnymi środowiskami,
wystąpią różnice ze względu na zasadniczo różne wymagania. Np.
  <ul>
      <li>na produkcji używany jest kontener zawierający kod źródłowy bez żadnych zależności testowych</li>
      <li>na CI używany jest kontener zawierający kod źródłowy z zależnościami testowymi</li>
      <li>lokalnie używany jest kontener, który montuje kod źródłowy z lokalnego hosta (w tym zależności)</li>
  </ul>

  |*  W niniejszej publikacji zamieściłem tylko część rozwiązania, które ułatwia implementacje
  | systemów monitoringu, modułów metryk, skalowanie całego systemu np. przy pomocy Kubernetes.

 Całość struktury jest zarządzana w bardzo łatwy i elastyczny sposób za pomocą (samodokumentujących się)
skryptów Makefile,
oczędza czas i pieniądze.

  Niniejsze skrypty Makefile są uruchamiane w systemach linux Debian 10/11,
dla systemów Windows 10/11 są w trakcie testów i rozwoju.

** Setup **
  02. [x] Budowanie, uruchamianie i testowanie kontenerów Docker

  [komendy w konsoli]:
    $ make

    $ make make-init
      (w przypadku braku Makefile wykonać komendy =>):
      $ cp .make/.env.example .make/.env && for variable in ; do echo $variable | tee -a .make/.env; done

    $ make docker-build
      =>
  $ cp ./.docker/.env.example ./.docker/.env && \
  ENV=local TAG=latest DOCKER_REGISTRY=docker.io DOCKER_NAMESPACE=kosowski76 APP_USER_ID=1010 APP_GROUP_ID=1011 APP_USER_NAME=develop docker compose -p shopb2biteo_local --env-file ./.docker/.env -f ./.docker/docker-compose/docker-compose-php-host.yml build php-host && \
  ENV=local TAG=latest DOCKER_REGISTRY=docker.io DOCKER_NAMESPACE=kosowski76 APP_USER_ID=1010 APP_GROUP_ID=1011 APP_USER_NAME=develop docker compose -p shopb2biteo_local --env-file ./.docker/.env -f ./.docker/docker-compose/docker-compose.yml -f ./.docker/docker-compose/docker-compose.local.yml build

    $ make docker-up
      =>
  $ ENV=local TAG=latest DOCKER_REGISTRY=docker.io DOCKER_NAMESPACE=kosowski76 APP_USER_ID=1010 APP_GROUP_ID=1011 APP_USER_NAME=develop docker compose -p shopb2biteo_local --env-file ./.docker/.env -f ./.docker/docker-compose/docker-compose.yml -f ./.docker/docker-compose/docker-compose.local.yml up -d 

    $ make docker-test
      =>
  $ bash ./.docker/docker-test.sh

  ! Jak widać praca jest owiele wygodniejsza i bardziej efektywna 




