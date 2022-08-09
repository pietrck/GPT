<?php

class Views
{
	public function render($filename)
	{
		$file = file_get_contents(__DIR__."/../../views/".$filename.".html");
		return $file;
	}

	public function page($name, $tags = [], $scripts = [])
	{
		$page = $this->render($name);
		$page = str_replace("{{menu}}", $this->render("menu"), $page);
		$page = str_replace("{{header}}", $this->render("header"), $page);
		$script = $this->render("scripts");

		foreach ($scripts as $key => $value) {
			$script .= $this->render($value);
		}

		$page = str_replace("{{scripts}}", $script, $page);

		foreach ($tags as $key => $value) {
			$page = str_replace("{{".$key."}}", $value, $page);
		}

		$page = preg_replace("/({{)(.*?)(}})/", "", $page);
		return $page;
	}
}

