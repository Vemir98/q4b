<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 06.05.2017
 * Time: 6:05
 */
$detector = new Mobile_Detect;
$max_left_pages = $detector->isMobile() ? 3 : $max_left_pages;
$max_right_pages = $detector->isMobile() ? 3 : $max_right_pages;
?>

<div class="q4-pagination">
    <ul class="pagination">
        <?php if ($previous_page !== FALSE): ?>
            <li class="prev_arrow"><a href="<?php echo HTML::chars($page->url($previous_page)) ?>"></a></li>
        <?php else: ?>
            <li class="prev_arrow"><a href="#"></a></li>
        <?php endif ?>

        <?php
        /* max left links */
        $offset = $total_pages - ($total_pages - $current_page);

        $left = $offset > $max_left_pages ? $max_left_pages : $offset;

        if ($offset > 1)
            for ($i = $offset - $left + 1; $i < $offset; $i++):
                ?>

                <li><a href="<?php echo HTML::chars($page->url(abs($i))) ?>"><?php echo abs($i) ?></a></li>

            <?php endfor ?>

        <?php
        /* max right links */
        $right = $current_page + $max_right_pages;

        for ($i = $current_page; $i <= $right && $i <= $total_pages; $i++):
            ?>

            <?php if ($i == $current_page): ?>
            <li class="active"><a href="#"><?php echo $i ?></a></li>
        <?php else: ?>
            <li><a href="<?php echo HTML::chars($page->url($i)) ?>"><?php echo $i ?></a></li>
        <?php endif ?>

        <?php endfor ?>

        <?php if ($next_page !== FALSE): ?>
            <li class="next_arrow"><a href="<?php echo HTML::chars($page->url($next_page)) ?>" rel="next"></a></li>
        <?php else: ?>
            <li class="next_arrow"><a href="#" rel="next"></a></li>
        <?php endif ?>

    </ul>
</div>

