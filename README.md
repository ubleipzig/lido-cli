# LIDO CLI

Lido Cli is a command line tool to process LIDO xml data for SOLR import.

## Installation

Clone repository via git. Change to root directory of lido-cli and execute:

<pre>
composer install
</pre>

to load all dependencies via composer.

Make sure you have [mbstring](http://php.net/manual/en/mbstring.installation.php) extension enabled.

## Usage

To get an first overview over functions move to lido-cli/src and call

<pre>
php ./lido-cli.php -h
</pre>

to list available usage.

To run the lido-cli programme please note that the (-i|--import) parameter is required with a valid LIDO XML source file.

<pre>
php ./lido-cli.php -i /path/to/lido-xml
</pre>

Lido Cli follows the concept to be scalable in two directions to keep usability to manage different Lido sources.
1) Run the output to a given Solr scheme with parameter (-s|schema)
2) To process or normalize possible various LIDO xml interpretation (-r|record)

<pre>
php ./lido-cli.php -i /path/to/lido-xml -r daphne -s vufind
</pre>

The example will process Daphne LIDO data of Staatliche Kunstsammlungen Dresden toward the VuFind Solr schema. If no parameter indicated the programme outputs native data of the NatLibFi LidoRecord class.

### Output

Output depends on given parameter (-e|--export) for an data directory where to store output in as line delimited json (*.ldj).

If no export parameter given lido-cli outputs Solr arrays on the screen for example evaluating processed content by the fly.

## License
[General Public License 3](http://www.gnu.org/licenses/gpl.html) or later; See also the LICENSE.txt file.
