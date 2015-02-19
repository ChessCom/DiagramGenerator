Chess Diagram Generator
===
Library to dynamically generate chess diagrams

## Usage

### Step 1: install library via composer:

**Notice: you need [Imagick](http://pecl.php.net/package/imagick) library installed**

Add it to respositories section:

```json
{
    "type": "git",
    "url": "git@github.com:ChessCom/DiagramGenerator.git"
}
```

Add it to require section:

```json
"chesscom/diagram-generator": "dev-master"
```

### Step 2: create generator

You can define it as a service

```xml
<service id="diagram.generator" class="DiagramGenerator\Generator">
    <argument type="service" id="validator" />
</service>
```

or create with `new` operator

```php
$generator = new \DiagramGenerator\Generator($validator);
```

### Step 3: create config

Config is as a `\DiagramGenerator\Config` object

```php
$config = new Config();
$config
    ->setFen($fenString)
    ->setSizeIndex(2)
    ...
```

If config parameters can be represented as array (e.g. GET or POST parameters), jms serializer can be used to crete config object:

```php
$diagramConfig = $jmsSerializer->deserialize($inputParameters, 'DiagramGenerator\Config', 'json');
```

#### Config options

| Name        | Description                     | Values                       | Default      | Examples                    |
| ----------- | ------------------------------- | ---------------------------- | ------------ | --------------------------- |
| fen         | board fen                       | a valid fen (only with pieces info) | default fen  | `/diagram?fen=rnbqkbnr/pp1ppppp/8/2p5/4P3/5N2/PPPP1PPP/RNBQKB1R`
| size        | cell size                       | 20px - 200px                 | 20px         | `/diagram?size=50px`        |
| piece       | piece theme                     | below (Piece themes list)   | modern       | `/diagram?piece=3d_wood`    |
| board       | board texture                   | below (Board textures list) | None         | `/diagram?board=bubblegum`  |
| caption     | text to show under the board    | any url encoded string       | empty string | `/diagram?caption=php%20c#` |
| coordinates | show or not board coordinates   | true or false                | false        | `/diagram?coordinates=true` |
| light       | hex color of light board pieces | ^[a-fA-F0-9]{6}$             | eeeed2       | `/diagram?light=aecef2`     |
| dark        | hex color of dark board pieces  | ^[a-fA-F0-9]{6}$             | 769656       | `/diagram?dark=16a656`      |
| flip        | allows to flip the board        | true or false                | false        | `/diagram?flip=true`        |
| highlight_squares | The squares that are highlighted | A string containing a set of squares. An example: 'a1a3h1g4' | '' | `/diagram?highlight_squares=h2h4` |
| highlight_squares_color | The color of the highlighted squares | ^[a-fA-F0-9]{6}$ | ffcccc | `/diagram?highlight_squares=h2h4&highlight_squares_color=eeeed2` |

#### Piece themes list: ####
3d_chesskid, 3d_plastic, 3d_staunton, 3d_wood, alpha, blindfold, book, bubblegum, cases, classic, club, condal, dark, game_room, glass, gothic, graffiti, light, lolz, marble, maya, metal, mini, modern, nature, neon, newspaper, ocean, sky, space, tigers, tournament, vintage, wood

#### Board textures list: ####

blackwhite, blue, brown, bubblegum, burled_wood, dark_wood, glass, graffiti, green, light, lolz, marble, marbleblue, marblegreen, metal, neon, newspaper, orange, parchment, purple, red, sand, sky, stone, tan, tournament, translucent, woodolive

### Step 4: create and render diagram

Its as simple as just call one method and set appropriate header:

```php
$diagram = $generator->buildDiagram($diagramConfig);
header('Content-Type: image/jpeg');
echo $diagram->getImage();
```

### Step 5: UrlHelper

UrlHelper class adds support of creating diagram image urls
To generate secure and non-secure urls:

```php
$urlHelper->getNonSecureUrl($config, $routingName);
$urlHelper->getSecureUrl($config, $routingName);
```
where $config is a `DigramGenerator\Config` object, `$routingName` is your routing name for the action, responsible for rendering diagrams

### Step 6: Validation

To enable config validation enable annotations for the validator

```php
$validator->enableAnnotationMapping();
```

or in sf2 config:

```yaml
framework:
    validation: { enable_annotations: true }
```

### Testing

Clone the library, run php composer.phar install, and run php vendor/bin/phpunit to run the unit tests.

### Bonus

`Fen` object is a convenient way to work with fen strings and represents the board as a list of all pieces, each one is an instance of `DiagramGenerator\Fen\Piece` object, has color, row and column properties. It can be developed according to our future needs
