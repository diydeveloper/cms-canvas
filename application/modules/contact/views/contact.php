<form<?php echo (($anchor) ? ' action="' . current_url() . $anchor . '"' : '')  . ' method="post"' . (($id) ? ' id="' . $id . '"' : '') . (($class) ? ' class="' . $class . '"' : ''); ?>>
    <?php if ($content): ?>
        <?php echo $content; ?>
    <?php else: ?>
        <div>
            <label for="name">Name:</a>
            <input type="text" name="name" id="name" />
        </div>

        <div>
            <label for="email">Email:</a>
            <input type="text" name="email" id="email" />
        </div>

        <div>
            <label for="phone">Phone:</a>
            <input type="text" name="phone" id="phone" />
        </div>

        <div>
            <label for="message">Message:</a>
            <textarea name="message" id="message"></textarea>
        </div>

        <?php if ($captcha): ?>
            <div>
                <span>
                    <label for="captcha">Please input the characters below:</label><br />
                    <img class="captcha_image" src="<?php echo site_url('contact/captcha'); ?>" /><br />
                    <input id="captcha" class="captcha_input" type="text" name="captcha_input" />
                </span>
            </div>
        <?php endif; ?>

        <div>
            <label for="submit"></a>
            <input type="submit" id="submit" value="Send" />
        </div>
    <?php endif; ?>

    <div style="display: none;">
        <input type="text" name="spam_check" value="" /> 
        <?php if ($id): ?>
            <input type="hidden" name="form_id" value="<?php echo $id; ?>" />
        <?php endif; ?>
    </div>
</form>