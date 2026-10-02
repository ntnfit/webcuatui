<?php

namespace App\Services\News\Writing;

use RuntimeException;

/** The article is not publishable (model said no, too short, copied...). The item becomes "rejected", not "failed". */
class ArticleRejectedException extends RuntimeException {}
