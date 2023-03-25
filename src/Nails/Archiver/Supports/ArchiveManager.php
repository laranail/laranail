<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Archiver\Supports;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Simtabi\Laranail\Nails\Archiver\Abstracts\Extractor;
use Simtabi\Laranail\Nails\Archiver\Services\Tar;
use Simtabi\Laranail\Nails\Archiver\Services\TarGz;
use Simtabi\Laranail\Nails\Archiver\Services\Zip;

final class ArchiveManager
{
    /** @var Extractor[] */
    protected array $extractorsMap = [];

    public function __construct()
    {
        $this->addExtractor('zip',    new Zip())
             ->addExtractor('tar',    new Tar())
             ->addExtractor('tar.gz', new TarGz());
    }

    /**
     * @param  string  $archiveExtension
     * @return Extractor
     *
     * @throws InvalidArgumentException
     */
    public function getExtractor(string $archiveExtension): Extractor
    {
        $extractor = Arr::get($this->extractorsMap, $archiveExtension);

        if (! $extractor) {
            throw new InvalidArgumentException("There are no extractors for extension '{$archiveExtension}'!");
        }

        return $extractor;
    }

    /**
     * @param  string  $archiveExtension
     * @param  Extractor  $instance
     * @return self
     */
    public function addExtractor(string $archiveExtension, Extractor $instance): self
    {
        $this->extractorsMap[$archiveExtension] = $instance;

        return $this;
    }
}
