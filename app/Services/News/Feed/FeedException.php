<?php

namespace App\Services\News\Feed;

use RuntimeException;

/** A feed could not be downloaded or parsed. Always scoped to one source. */
class FeedException extends RuntimeException {}
