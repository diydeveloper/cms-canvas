<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image_cache 
{
    public $CI               = '';
    public $source_image     = '';
    public $height           = 0;
    public $width            = 0;
    public $crop             = FALSE;
    public $no_image_image   = '';
    public $new_image        = '';
    public $replace_original = FALSE;

    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->library('image_lib');
    }

    // ------------------------------------------------------------------------

    function clear()
    {
        $this->source_image     = '';
        $this->height           = 0;
        $this->width            = 0;
        $this->crop             = FALSE;
        $this->no_image_image   = '';
        $this->new_image        = '';
        $this->replace_original = FALSE;
    }

    // ------------------------------------------------------------------------

    function initialize($props = array())
    {
		if (count($props) > 0)
		{
			foreach ($props as $key => $val)
			{
				$this->$key = $val;
			}
		}

        // Check that source image exists else use not found image
        $this->_source_image();

        // Determine new image path and filename
        $this->_new_image();

    }

    // ------------------------------------------------------------------------

    function image_cache()
    {
        // Return if source image not defined
        if ($this->source_image == '')
        {
            return;
        }

        // The first time the image is requested
        // Or the original image is newer than our cache image
        if (( ! file_exists(FCPATH . $this->new_image)) || filemtime(FCPATH . $this->new_image) < filemtime(FCPATH . $this->source_image) || $this->replace_original) 
        {
            if ( ! is_dir(FCPATH . dirname($this->new_image)))
            {
                mkdir(FCPATH . dirname($this->new_image), 0777, TRUE);
            }
                
            if ($this->crop)
            {
                $this->_crop();
            }
            else
            {
                $this->_resize();
            }
        }
         
        return site_url($this->new_image);
    }

    // ------------------------------------------------------------------------

    function _source_image()
    {
        $this->source_image = trim(urldecode($this->source_image), '/');

        // Alternative image if file was not found
        if ( ! file_exists(FCPATH . $this->source_image) || $this->source_image == '')
        {
            $this->source_image = trim(urldecode($this->no_image_image), '/');
        }
    }

    // ------------------------------------------------------------------------

    function _new_image()
    {

        $new_image = trim($this->new_image, '/');
        $image_cache = trim(IMAGE_CACHE, '/');

        // Return if source image not defined
        if ($this->source_image == '')
        {
            return;
        }

        //The new generated filename we want
        $info = pathinfo($this->source_image);

        $filename = $info['filename'];                                                                                                                          
        $ext = $info['extension'];                                                                                                                          
        $dirname = $info['dirname'];

        if ($this->replace_original)
        {
            $new_image = $this->source_image;
        }
        else if ( ! empty($new_image))
        {
            // If filename and ext not specified use default
            if ($filename == ''  && $ext == '')
            {
                if ($this->crop)
                {
                    $new_image = $new_image . '/' . $filename . '-' . $this->width . 'x' . $this->height . 'c' . '.' . $ext;
                }
                else
                {
                    $new_image = $new_image . '/' . $filename . '-' . $this->width . 'x' . $this->height . '.' . $ext;
                }
            }
        }
        else
        {
            if ($this->crop)
            {
                $new_image = $image_cache . '/' . md5($dirname . '/' . $filename . '.' . $ext) . '-' . $filename . '-' . $this->width . 'x' . $this->height . 'c' . '.' . $ext;
            }
            else
            {
                $new_image = $image_cache . '/' . md5($dirname . '/' . $filename . '.' . $ext) . '-' . $filename . '-' . $this->width . 'x' . $this->height . '.' . $ext;
            }
        }

        $this->new_image = $new_image;
    }


    // ------------------------------------------------------------------------

    function _crop()
    {
        //The original sizes
        $original_size = getimagesize(FCPATH . $this->source_image);
        $original_width = $original_size[0];
        $original_height = $original_size[1];
        $ratio = $original_width / $original_height;

        //The requested sizes
        $requested_width = $this->width;
        $requested_height = $this->height;

        //Initialising
        $new_width = 0;
        $new_height = 0;
        
        //Calculations
        if ($requested_width > $requested_height) 
        {
            $new_width = $requested_width;
            $new_height = $new_width / $ratio;
            if ($requested_height == 0)
            {
                $requested_height = $new_height;
            }
            
            if ($new_height < $requested_height) 
            {
                $new_height = $requested_height;
                $new_width = $new_height * $ratio;
            }
        
        }
        else 
        {
            $new_height = $requested_height;
            $new_width = $new_height * $ratio;
            if ($requested_width == 0)
            {
                $requested_width = $new_width;
            }
            
            if ($new_width < $requested_width) 
            {
                $new_width = $requested_width;
                $new_height = $new_width / $ratio;
            }
        }
        
        $new_width = ceil($new_width);
        $new_height = ceil($new_height);
        
        //Resizing
        $config = array();
        $config['image_library'] = 'gd2';
        $config['source_image'] = $this->source_image;
        $config['new_image'] = $this->new_image;
        $config['maintain_ratio'] = FALSE;
        $config['height'] = $new_height;
        $config['width'] = $new_width;
        $this->CI->image_lib->clear();
        $this->CI->image_lib->initialize($config);
        $this->CI->image_lib->resize();
        $this->CI->image_lib->clear();
        
        //Crop if both width and height are not zero
        if (($this->width != 0) && ($this->height != 0)) 
        {
            $x_axis = floor(($new_width - $this->width) / 2);
            $y_axis = floor(($new_height - $this->height) / 2);
            
            //Cropping
            $config = array();
            $config['source_image'] = $this->new_image;
            $config['maintain_ratio'] = FALSE;
            $config['new_image'] = $this->new_image;
            $config['width'] = $this->width;
            $config['height'] = $this->height;
            $config['x_axis'] = $x_axis;
            $config['y_axis'] = $y_axis;
            $this->CI->image_lib->clear();
            $this->CI->image_lib->initialize($config);
            $this->CI->image_lib->crop();
            $this->CI->image_lib->clear();
        }
    }

    // ------------------------------------------------------------------------

    function _resize()
    {
        //The original sizes
        $original_size = getimagesize(FCPATH . $this->source_image);
        $original_width = $original_size[0];
        $original_height = $original_size[1];
        $ratio = $original_width / $original_height;

        //The requested sizes
        $requested_width = $this->width;
        $requested_height = $this->height;

        // Keep current dimensions if the requested are both are less than the current
        if ($original_width < $requested_width && $original_height < $requested_height)                                                                                                                
        {                                                                                                                                                               
            $requested_width = $original_width;                                                                                                                                        
            $requested_height = $original_height;                                                                                                                                      
        }                                                                                                                                                               

        if ($requested_width == 0)
        {
            $requested_width = $original_width;
        }

        if ($requested_height == 0)
        {
            $requested_height = $original_height;
        }
                                                                                                                                                                        
        // Resize Image                                                                                                                                                 
        $config = array();                                                                                                                                              
        $config['image_library']    = 'gd2';                                                                                                                            
        $config['source_image']     = $this->source_image;                                                                                                                      
        $config['new_image']        = $this->new_image;                                                                                                                     
        $config['maintain_ratio']   = TRUE;                                                                                                                             
        $config['height']           = $requested_height;                                                                                                                          
        $config['width']            = $requested_width;                                                                                                                           
        $this->CI->image_lib->clear();
        $this->CI->image_lib->initialize($config);                                                                                                                            
        $this->CI->image_lib->resize();                                                                                                                                       
        $this->CI->image_lib->clear();                                                                                                                                        
    }
}
