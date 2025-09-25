<div class="changelog_container">
    <?php
    $changelog_entries = get_changelog_from_readme();
    if (!empty($changelog_entries)) :
        foreach ($changelog_entries as $entry) :
            $version = esc_html($entry[1]);
            $date = esc_html($entry[2]);
            $details = explode("\n", trim($entry[3]));
            ?>
            <div class="changelog_element">
                <span class="theme_version">
                    <strong><?php echo 'v' . $version; ?></strong>
                    <?php echo 'Release date: ' . $date; ?>
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </span>

                <div class="changelog_details" style="display: none;">
                    <ul>
                        <?php foreach ($details as $detail) : ?>
                            <li><?php echo esc_html(trim($detail, "- \t")); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php
        endforeach;
    else :
        ?>
        <p><?php esc_html_e('No changelog available.', 'wp-shop-woocommerce'); ?></p>
    <?php endif; ?>
</div>