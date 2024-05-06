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

  !! i działa odrazu po wyjęciu z pudełka


  03. [x] Symfony setup and run initial tests.

    $ make execute-in-container DOCKER_SERVICE_NAME="application" COMMAND='composer create-project symfony/skeleton /tmp/symfony --no-install --no-scripts'

    $ rm -rf appservice/public/ appservice/tests/ appservice/composer.* appservice/phpunit.xml
    $ make execute-in-container DOCKER_SERVICE_NAME="application" COMMAND="bash -c 'mv -n /tmp/symfony/{.*,*} .' && rm -f /tmp/symfony"

    $ make composer ARGS='install'
    $ make composer ARGS='require --dev "phpunit/phpunit"'

    $ make test


 ### 02. Problem solutions
 
  01. [x] Sformułowanie i analiza problemu,

    - przygotowanie podstawowych diagramów użycia
    - projektowanie
    - zaprojektowanie modeli 

    - przygotowanie ogólnych testów (Test Driven Development)
    - projektowanie funkcjonalności
    - testowanie
    - ekspoloatacja

  02. [x] Przygotowanie podstawowych diagramów użycia

 ## Sklep B2B e-commerce - Diagram przypadków użycia
  <details>
  <summary><b>Kliknij tutaj:</b> <i>Sklep B2B e-commerce</i> - Diagram przypadków użycia!</summary>

   * ![diagram1 local view](./var/images/01_01_customer-order_use-diagram.jpg)
  </details>

  03. [ ] Projektowanie: 
  
    - Wybranie kolejności zadań: 34, 36, 35,
    Głównym zadaniem jest 35 - 'Przesłanie zamówienia do CRM', aby móc je wykonać w ciągu,
    wybrałem w pierwszej kolejności taski od których to zadanie jest zależne
    36 - 'Walidacja zamówienia', który jest zależny od zadania 34 - 'Saldo klienta', oraz
    dodatkowej funkcjonalnośi weryfikującej 'Zlecenie' pod względem ilości produktów i wagi.
    Co w pierwszej kolejności wymaga utworzenia kona użytkownika, jego uwierzytelnienia
    i autoryzacji.



    - [ ] Projektowanie przykładowych testów dla schematów Doctrine

      $ make composer ARGS="require doctrine/orm"
      $ make composer ARGS="require doctrine/doctrine-bundle"

    - [ ] Token JWT

      $ mkdir -p config/jwt

      $ make composer ARGS='require "lexik/jwt-authentication-bundle"'

      $ make execute-in-container DOCKER_SERVICE_NAME="application" COMMAND="php bin/console lexik:jwt:generate-keypair"
      $ make execute-in-container DOCKER_SERVICE_NAME="application" COMMAND="chmod 644 config/jwt/public.pem config/jwt/private.pem"

      $ make composer ARGS="require --dev orm-fixtures"

      $ make composer ARGS="require symfony/uid"




    - [ ] Utworzenie użytkownika i systemu logowania




    - [ ] Utworzenie Modeli - Encji

      w jakim sensie jest pojęcie 'Saldo'? - Saldo wewnętrzne CRM czy zewnętrzne bankowe?
      jedno konto płatnicze, kanał płatniczy czy kilka? 

    {
      "orderId": uuid,
      "clientId": uuid,
      "products": [
        {
          "productId": string,
          "quantity": int,
          "price": float,
          "weight": float
        }
      ]
    }

      w order details
       Request "productId": string, w sytemie CRM Request "productId": uuid,
       "guantity": int - ilość produktu danego rodzaju określonego 'productId'
       "price": float - cena jednej jednostki/sztuki produktu 'productId' o określonej wadze "weight": float,
       "weight": float - waga jednej jednostki produku

    - [ ] Definicja schematów Doctrine
  
    Jest to wersja dla środowiska developerskiego więc mogą występować dodatkowo klasy,
    pola, metody, funkcje itp., nie wykorzystane lub zakomentowane, (generalnie clean code, 
    comments less, use only important comments at development environment)
    ale umieściłem je z zamysłem co było zaznaczone w specyfijcaji zadań,
    jest to tylko część sugestii, które powinny być już wcześniej zaprojektowane
    na etapie projektowania problemu i jego rozwiązania,

    Ilość testów ograniczona, wykonane tylko dla celów prezentacyjnych i merytorycznych.

    W przypadku problemu z uruchomieniem pod systemami Linux, Mac z powodu uprawinień
    istnieje kilka rozwiązań problemu, niektóre propozycje rozwiązania:

    - uruchamiać aplikację z poziomu root (nie rekomendowane, odradzam !!!)
    - ustawienie uprawnień dla wszystkich użytkowników (nie rekomendowane - problemy!)
    - ustawienie użtkownika dla kontenerów (rekomendowane)
    i inne.
    Niniesze problemy nie są tematem tej publikacji więc nie kładę nacisku 
    na wybór rozwiązania, pozostawiam to indiwidualnym możliwościom.

    $ make execute-in-container DOCKER_SERVICE_NAME="application" COMMAND="bin/console doctrine:schema:drop --full-database --force"
    $ make execute-in-container DOCKER_SERVICE_NAME="application" COMMAND="php bin/console doctrine:schema:update --force"

    - [ ] Utworzenie kilku przykładowych fixtures

    $ make composer ARGS="require symfony/security-bundle"

    $ make composer ARGS="require --dev orm-fixtures"
    $ make execute-in-container DOCKER_SERVICE_NAME="application" COMMAND="php bin/console doctrine:fixtures:load"

    - [ ]
  ! Jak widać praca jest owiele wygodniejsza i bardziej efektywna 


