<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\SonataBlockConfig;

return static function (SonataBlockConfig $sonataBlock): void {
    $sonataBlock->httpCache(false);

    $sonataBlock->defaultContexts(['sonata_page_bundle']);
    $sonataBlock->block('sonata.admin.block.stats')
        ->contexts(['admin'])
    ;
    $sonataBlock->block('sonata.admin.block.admin_list')
        ->contexts(['admin'])
    ;
    $sonataBlock->block('sonata.admin.block.search_result')
        ->contexts(['admin'])
    ;

    $serviceText = $sonataBlock->block('sonata.block.service.text');
    $serviceText->template()
        ->name('homeless')
        ->template('Block/block_text.html.twig')
    ;

    $sonataBlock->block('sonata.block.service.rss');
    $sonataBlock->block('sonata.block.service.ckeditor');
    $sonataBlock->block('sonata.block.service.request_form');

    // Ecommerce bundles
    $sonataBlock->block('sonata.order.block.recent_orders');
    $sonataBlock->block('sonata.product.block.recent_products');
    $sonataBlock->block('sonata.product.block.similar_products');
    $sonataBlock->block('sonata.product.block.categories_menu');
    $sonataBlock->block('sonata.product.block.filters_menu');
    $sonataBlock->block('sonata.product.block.variations_form');
    $sonataBlock->block('sonata.customer.block.recent_customers');

    $sonataBlock->block('sonata.basket.block.nb_items')
        ->contexts(['user'])
    ;
    $sonataBlock->block('sonata.user.block.account')
        ->contexts(['user'])
    ;
    $sonataBlock->block('sonata.user.block.menu')
        ->contexts(['user'])
    ;

    // Some specific block from the SonataMediaBundle
    $sonataBlock->block('sonata.media.block.media');
    $sonataBlock->block('sonata.media.block.gallery');
};
