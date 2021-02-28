<?php
/** @var $theme string */

$isDarkMode = $theme === 'darkly';
?><li>
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-dark {{ $isDarkMode ? '' : 'active' }}">
            <input type="radio" id="theme_light_mode" class="theme_switch_btn" autocomplete="off"
                   data-theme="superhero" {{ $isDarkMode ? '' : 'checked' }}>
            <i class="fas fa-sun"></i>
        </label>
        <label class="btn btn-dark {{ $isDarkMode ? 'active' : '' }}">
            <input type="radio" id="theme_dark_mode" class="theme_switch_btn" autocomplete="off"
                   data-theme="darkly" {{ $isDarkMode ? 'checked' : '' }}>
            <i class="fas fa-moon"></i>
        </label>
    </div>
</li>