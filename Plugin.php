<?php namespace SunLab\ForumUpload;

use Illuminate\Support\Facades\Lang;
use SunLab\ForumUpload\Models\Settings;
use System\Classes\PluginBase;
use Winter\Forum\Components\Topic;
use Winter\Forum\Models\Post;
use Winter\Storm\Exception\ValidationException;
use Winter\Storm\Support\Facades\Validator;
/**
 * ForumUpload Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [
        'Winter.Forum'
    ];

    public function pluginDetails(): array
    {
        return [
            'name'        => 'sunlab.forumupload::lang.plugin.name',
            'description' => 'sunlab.forumupload::lang.plugin.description',
            'author'      => 'SunLab',
            'icon'        => 'icon-leaf'
        ];
    }

    public function boot(): void
    {
        /**
         * Create the content_images relationship to the Winter.Forum Post model
         */
        Post::extend(function ($postModel) {
            $postModel->attachMany['content_images'] = ['System\Models\File'];
        });

        /**
         * Extends the Winter.Forum Topic component
         */
        Topic::extend(function ($topicComponent) {
            /**
             * Create a `onFileUpload` AJAX handler to handle file upload, using deferred bindings
             */
            $topicComponent->addDynamicMethod('onFileUpload', function () {
                $postId = post('postId');

                // Generate a session key if needed
                $sessionKey = !empty(post('sessionKey')) ? post('sessionKey') : uniqid('session_key', true);

                $uploadedFiles = request()->allFiles();
                $storedFiles = [];
                $post = $postId ? Post::find($postId) : new Post;

                /**
                 * Build the validation rules corresponding to the current Plugin's settings
                 */
                $settings = Settings::instance();
                $validationRules = [];
                if ($settings->file_extensions) {
                    $validationRules['*'][] = 'mimes:'. str_replace(' ', '', $settings->file_extensions);
                }

                if ($settings->max_filesize) {
                    $validationRules['*'][] = 'max:'. $settings->max_filesize;
                }

                $errorMessages = [
                    '*.mimes' => Lang::get('system::validation.mimes', ['attribute' => Lang::get('sunlab.forumupload::lang.errors.file_upload_attribute')]),
                    '*.max' => Lang::get('system::validation.max', ['attribute' => Lang::get('sunlab.forumupload::lang.errors.file_upload_attribute')]),
                ];
                $validator = Validator::make($uploadedFiles, $validationRules, $errorMessages);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                // File count manual validation
                $nbFiles = count($uploadedFiles);
                $countValidation = true;
                // Verify if the request files does not overflow the files_per_post limits
                if ($nbFiles > $settings->files_per_post) {
                    $countValidation = false;
                }

                // If ok, adds deferred bindings and already attached files to the count
                if (
                    $countValidation &&
                    $nbFiles + $post->content_images()->withDeferred($sessionKey)->count() > $settings->files_per_post
                ) {
                    $countValidation = false;
                }

                /**
                 * Throw an exception if the files per post limits is exceeded
                 */
                if (!$countValidation) {
                    throw new ValidationException(
                        [Lang::get('sunlab.forumupload::lang.errors.files_per_post', ['count' => $settings->files_per_post])]
                    );
                }

                // If everything is okay, store the files bindings and return their server's data
                foreach ($uploadedFiles as $file) {
                    $storedFile = $post->content_images()->create(['data' => $file], $sessionKey);
                    $fileName = $file->getClientOriginalName();

                    $storedFiles[] = [
                        'id' => $storedFile->id,
                        'originalName' => $fileName,
                        'name' => pathinfo($fileName, PATHINFO_FILENAME),
                        'url' => $storedFile->getPath()
                    ];
                }

                return [
                    'sessionKey' => $sessionKey,
                    'files' => $storedFiles
                ];
            });

            /**
             * Add event listeners to Topic component for Post create and update events
             * This will attach
             */
            $topicComponent->bindEvent('topic.create', function ($topic) {
                if ($sessionKey = post('sessionKey')) {
                    $topic->first_post->commitDeferred($sessionKey);
                }
            });

            $topicComponent->bindEvent('topic.post', function ($post) {
                if ($sessionKey = post('sessionKey')) {
                    $post->commitDeferred($sessionKey);
                }
            });

            $topicComponent->bindEvent('topic.post-update', function ($post) {
                if ($sessionKey = post('sessionKey')) {
                    $post->commitDeferred($sessionKey);
                }
            });
        });
    }

    public function registerPermissions()
    {
        return [
            'sunlab.forumupload::lang.settings.menu' => [
                'tab'   => 'sunlab.forumupload::lang.settings.menu',
                'label' => 'sunlab.forumupload::lang.settings.menu_desc'
            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'sunlab.forumupload::lang.settings.menu',
                'description' => 'sunlab.forumupload::lang.settings.menu_desc',
                'icon'        => 'icon-download',
                'class'       => Settings::class,
                'category'    => 'winter.forum::lang.plugin.name',
                'order'       => 600,
                'permissions' => ['sunlab.forumupload::lang.settings.menu'],
            ]
        ];
    }
}
