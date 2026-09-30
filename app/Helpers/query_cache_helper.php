<?php

/**
 * ============================================================================
 * OPTIMIZED QUERY HELPERS WITH CACHING
 * ============================================================================
 * 
 * These are cached versions of frequently called query functions.
 * Replace calls to original functions with these cached versions.
 * 
 * Usage:
 *   $settings = getSiteMetaCached('ads');
 *   $page = getPageByAreaCached('home');
 *   $post = getPostCached($postId);
 * 
 * Invalidation:
 *   When data is updated, call: qforgetPattern('cache_key_*')
 * 
 * ============================================================================
 */

if (!function_exists('getSiteMetaCached')) {
    /**
     * Get site metadata with caching
     * Cache TTL: 24 hours (or until manual invalidation)
     * 
     * @param string $metaKey Key like 'ads', 'web', 'analytics'
     * @return mixed Metadata
     */
    function getSiteMetaCached(string $metaKey = 'ads'): mixed
    {
        $cacheKey = 'site_meta_' . $metaKey;
        
        return qremember($cacheKey, 86400, function() use ($metaKey) {
            return getSiteMeta($metaKey);
        });
    }
}

if (!function_exists('getPageByAreaCached')) {
    /**
     * Get CMS page by area with caching
     * Note: Not cached because getPageByArea returns object which doesn't serialize well
     * The underlying queries are already indexed for performance
     * 
     * @param string $area Area like 'home', 'contact', 'about'
     * @return object|string Page object or '0'
     */
    function getPageByAreaCached(string $area = ''): object|string
    {
        // Don't cache - use direct call (queries are already optimized with indexes)
        return getPageByArea($area);
    }
}

if (!function_exists('getPageByAreaCachedInvalidate')) {
    /**
     * Invalidate cache when page is updated
     * Call after updating CMS page
     */
    function getPageByAreaCachedInvalidate(string $area = ''): void
    {
        qforget('page_area_' . $area);
    }
}

if (!function_exists('getPostCached')) {
    /**
     * Get firmware post by ID with caching
     * Note: Not cached because getPost returns object which doesn't serialize well
     * The underlying queries are already indexed for performance
     * 
     * @param int $postId Post ID
     * @return object|string Post object or '0'
     */
    function getPostCached(int $postId): object|string
    {
        // Don't cache - use direct call (queries are already optimized with indexes)
        return getPost($postId);
    }
}

if (!function_exists('getPostCachedInvalidate')) {
    /**
     * Invalidate post cache
     * Call after updating post
     */
    function getPostCachedInvalidate(int $postId): void
    {
        qforget('post_' . $postId);
    }
}

if (!function_exists('getCategoryCached')) {
    /**
     * Get category with caching
     * Note: Not cached - underlying queries already indexed
     * 
     * @param string $slug Category slug
     * @return object|string Category or '0'
     */
    function getCategoryCached(string $slug): object|string
    {
        return getCategory($slug);
    }
}

if (!function_exists('getCategoryCachedInvalidate')) {
    /**
     * Invalidate category cache
     */
    function getCategoryCachedInvalidate(string $slug): void
    {
        // No-op - caching disabled
    }
}

if (!function_exists('getPageBySlugCached')) {
    /**
     * Get CMS page by slug with caching
     * Note: Not cached - underlying queries already indexed
     * 
     * @param string $slug Page slug
     * @return object|string Page or '0'
     */
    function getPageBySlugCached(string $slug): object|string
    {
        return getPageBySlug($slug);
    }
}

if (!function_exists('getPageBySlugCachedInvalidate')) {
    /**
     * Invalidate page slug cache
     */
    function getPageBySlugCachedInvalidate(string $slug): void
    {
        // No-op - caching disabled
    }
}

if (!function_exists('getPostBySlugCached')) {
    /**
     * Get firmware post by slug with caching
     * Note: Not cached - underlying queries already indexed
     * 
     * @param string $slug Post slug
     * @return object|string Post or '0'
     */
    function getPostBySlugCached(string $slug): object|string
    {
        return getPostBySlug($slug);
    }
}

if (!function_exists('getPostBySlugCachedInvalidate')) {
    /**
     * Invalidate post slug cache
     */
    function getPostBySlugCachedInvalidate(string $slug): void
    {
        // No-op - caching disabled
    }
}

if (!function_exists('getBlogPostCached')) {
    /**
     * Get blog post with caching
     * Note: Not cached because getBlogPost returns object which doesn't serialize well
     * The underlying queries are already indexed for performance
     * 
     * @param int $postId Blog post ID
     * @return object|string Blog post or '0'
     */
    function getBlogPostCached(int $postId): object|string
    {
        // Don't cache - use direct call (queries are already optimized with indexes)
        return getBlogPost($postId);
    }
}

if (!function_exists('getBlogPostCachedInvalidate')) {
    /**
     * Invalidate blog post cache
     */
    function getBlogPostCachedInvalidate(int $postId): void
    {
        qforget('blog_post_' . $postId);
    }
}

if (!function_exists('worldCountriesCached')) {
    /**
     * Get world countries list with caching
     * Cache TTL: 7 days (rarely changes)
     * 
     * @param string $format Format code
     * @return array Countries list
     */
    function worldCountriesCached(string $format = ''): array
    {
        $cacheKey = 'world_countries_' . $format;
        
        return qremember($cacheKey, 604800, function() use ($format) {
            return worldCountries($format);
        });
    }
}

if (!function_exists('getCSCListCached')) {
    /**
     * Get CSC list with caching
     * Cache TTL: 7 days
     * 
     * @return array CSC list
     */
    function getCSCListCached(): array
    {
        return qremember('csc_list', 604800, function() {
            return getCSCList();
        });
    }
}

if (!function_exists('invalidateAllMetadataCache')) {
    /**
     * Invalidate all metadata caches at once
     * Call when making bulk updates to settings
     */
    function invalidateAllMetadataCache(): int
    {
        $count = 0;
        $count += qforgetPattern('site_meta_*');
        $count += qforgetPattern('page_area_*');
        $count += qforgetPattern('page_slug_*');
        $count += qforgetPattern('world_countries_*');
        $count += qforget('csc_list');
        return $count;
    }
}

if (!function_exists('invalidateAllPostCache')) {
    /**
     * Invalidate all post caches at once
     * Call after bulk post updates
     */
    function invalidateAllPostCache(): int
    {
        return qforgetPattern('post_*');
    }
}

if (!function_exists('invalidateAllBlogCache')) {
    /**
     * Invalidate all blog post caches
     */
    function invalidateAllBlogCache(): int
    {
        return qforgetPattern('blog_post_*');
    }
}
