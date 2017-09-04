<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\SeoProduct;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function slugParsing($lang, $param1 = null, $param2 = null, $param3 = null)
    {
        $categorySlug = null;
        $subCategorySlug = null;
        if ($param1 === null) {
            return $this->pageViewMain($lang, $categorySlug, $subCategorySlug);
        }
        if ($product = Product::bySlug($lang, $param1)) {
            return $this->pageViewProduct($lang, $product, $categorySlug, $subCategorySlug);
        } else {
            $categorySlug = $param1;
            if ($param2 === null) {
                if ($products = Category::productsBySlug($lang, $categorySlug)) {
                    return $this->pageViewCategory($lang, $products, $categorySlug, $subCategorySlug);
                } elseif (isset($products)) {
                    return $this->pageViewCategory($lang, $products, $categorySlug, $subCategorySlug);
                } else {
                    return $this->pageView404();
                }
            }
            if ($product = Product::bySlug($lang, $param2)) {
                return $this->pageViewProduct($lang, $product, $categorySlug, $subCategorySlug);
            } else {
                $subCategorySlug = $param2;
                if ($param3 === null) {
                    if ($products = Category::productsBySlug($lang, $categorySlug, $subCategorySlug)) {
                        return $this->pageViewCategory($lang, $products, $categorySlug, $subCategorySlug);
                    } elseif (isset($products)) {
                        return $this->pageViewCategory($lang, $products, $categorySlug, $subCategorySlug);
                    } else {
                        return $this->pageView404();
                    }
                }
                if ($product = Product::bySlug($lang, $param3)) {
                    return $this->pageViewProduct($lang, $product, $categorySlug, $subCategorySlug);
                } else {
                    return $this->pageView404();
                }
            }
        }
    }
    private function pageViewMain($lang, $categorySlug, $subCategorySlug)
    {
        $about = true;

        return view('frontend.home-page')->with(compact('categorySlug', 'subCategorySlug', 'about'));
    }


    /**
     * Display Product page
     * @param $lang
     * @param $product
     * @param $categorySlug
     * @param $subCategorySlug
     * @return mixed
     */
    private function pageViewProduct($lang, $product, $categorySlug, $subCategorySlug)
    {
        $comments = $product->publishedComments;
        $similarProducts = $product->similarProducts($lang, $categorySlug);
        $seo = !empty($product->stock->seo) ? $product->stock->seo : new SeoProduct();
        $alternate = Product::getAlternateProducts($lang, $product->id);
        $productPage = true;

        return view('frontend.product')->with(compact('product', 'comments', 'similarProducts', 'categorySlug',
            'subCategorySlug', 'seo', 'productPage', 'alternate'));
    }
    /**
     * Display Category page
     * @param $lang
     * @param $products
     * @param $categorySlug
     * @param $subCategorySlug
     * @return mixed
     */
    private function pageViewCategory($lang, $products, $categorySlug, $subCategorySlug)
    {
        $seo = Category::categorySeo($lang, $categorySlug);
        $alternate = Category::alternateCategory($lang, $categorySlug);
        $breadcrumb = Category::breadcrumbGenerateBySlug($lang, $categorySlug);
        $categoryPage = true;

        return view('frontend.index')->with(compact('products', 'categorySlug', 'subCategorySlug', 'seo',
            'categoryPage', 'alternate', 'breadcrumb'));
    }
    /**
     * 404 page
     * @param int $errorCode
     * @return mixed
     */
    private function pageView404($errorCode = 404)
    {
        return view('frontend.order-error')->with(compact('errorCode'));
    }
}
