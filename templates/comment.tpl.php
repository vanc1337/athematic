<?php
/**
 * @file
 * Zen theme's implementation for comments.
 *
 * Available variables:
 * - $author: Comment author. Can be link or plain text.
 * - $content: An array of comment items. Use render($content) to print them all, or
 *   print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $created: Formatted date and time for when the comment was created.
 *   Preprocess functions can reformat it by calling format_date() with the
 *   desired parameters on the $comment->created variable.
 * - $changed: Formatted date and time for when the comment was last changed.
 *   Preprocess functions can reformat it by calling format_date() with the
 *   desired parameters on the $comment->changed variable.
 * - $new: New comment marker.
 * - $permalink: Comment permalink.
 * - $submitted: Submission information created from $author and $created during
 *   template_preprocess_comment().
 * - $picture: Authors picture.
 * - $signature: Authors signature.
 * - $status: Comment status. Possible values are:
 *   comment-unpublished, comment-published or comment-preview.
 * - $title: Linked title.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the following:
 *   - comment: The current template type, i.e., "theming hook".
 *   - comment-by-anonymous: Comment by an unregistered user.
 *   - comment-by-node-author: Comment by the author of the parent node.
 *   - comment-preview: When previewing a new or edited comment.
 *   - first: The first comment in the list of displayed comments.
 *   - last: The last comment in the list of displayed comments.
 *   - odd: An odd-numbered comment in the list of displayed comments.
 *   - even: An even-numbered comment in the list of displayed comments.
 *   The following applies only to viewers who are registered users:
 *   - comment-unpublished: An unpublished comment visible only to administrators.
 *   - comment-by-viewer: Comment by the user currently viewing the page.
 *   - comment-new: New comment since the last visit.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * These two variables are provided for context:
 * - $comment: Full comment object.
 * - $node: Node object the comments are attached to.
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_comment()
 * @see zen_preprocess_comment()
 * @see template_process()
 * @see theme_comment()
 */
?>
<div class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>


    <div class="comment-title-out-wrapper">
        <div class="comment-title-in-wrapper">
            <?php print render($title_prefix); ?>
            <?php if ($title): ?>
           <?php preg_match('/^(.*<a.*>)(.*)(<\/a>.*)$/', $title,$title_array)?>
<?php
            $chars_in_title=strlen($title_array[2]);
                if ($chars_in_title > 59) {
                    $title_text=truncate_text($title_array[2], 59);
                    $title=$title_array[1] . $title_text . $title_array[3];
                }
                ?>
                <h3<?php print $title_attributes; ?>>
                    <?php print $title; ?>
                    <?php if ($new): ?>
                        <span class="new"><?php print $new; ?></span>
                <?php endif; ?>
                </h3>
            <?php elseif ($new): ?>
                <div class="new"><?php print $new; ?></div>
            <?php endif; ?>
<?php print render($title_suffix); ?>
        </div></div><!-- /.comment-title-out-wrapper, .comment-title-in-wrapper -->


    <?php if ($status == 'comment-unpublished'): ?>
        <div class="unpublished"><?php print t('Unpublished'); ?></div>
<?php endif; ?>
    <div class="comment-content-top-left"><div class="comment-content-top-right">

            <div class="submitted-wrap"> <!-- Wrap submitted for themeing -->
                <div class="submitted">
                    <div class="center_submit"><?php print $picture; ?>
                    <?php print $permalink; ?>
<?php print $submitted; ?></div>
                </div>
            </div> <!-- /.submitted-wrap -->
        </div></div> <!-- /.comment-content-top-left, comment-content-top-right.-->
    <div class="comment-content-mid-container-left"><div class="comment-content-mid-container-right">

            <div class="content"<?php print $content_attributes; ?>>
                <?php
                // We hide the comments and links now so that we can render them later.
                hide($content['links']);
                print render($content);
                ?>
                    <?php if ($signature): ?>
                    <div class="user-signature clearfix">
                    <?php print $signature; ?>
                    </div>
<?php endif; ?>
            </div>

<?php print render($content['links']) ?>
        </div></div> <!-- /.comment-content-mid-container-left, .comment-content-mid-container-left-->
    <div class="comment-content-bottom-left"><div class="comment-content-bottom-right"></div></div> <!--/.comment-content-bottom-right, .comment-content-bottom-left -->
</div><!-- /.comment -->
