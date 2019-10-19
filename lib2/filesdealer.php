<?php

	/*
	*
	*	@Project  -> PHP , Class dealing with files ;
	*
	*	@Author   -> AmineDine ;
	*
	*	@Mail     -> AmineBtsr@outlook.com ;
	*
	*	@Facebook -> http://www.facebook.com/Aminebtsr ;
	*
	*/
	
	define ( 'BASEPATH' , realpath ( dirname ( __FILE__ ) ) . '/' , TRUE ) ;
	
	class DealingFiles
	
	{
	
		/*
		*
		*	Public variable.
		*
		*/
	
		private static $File ;
		
		/*
		*
		*	Method of dealing with the file.
		*
		*/
	
		private static $_Method = array
		
		(

			'ADD'	=> 'a' , 
			
			'WRITE'	=> 'w' , 
			
			'READ'	=> 'r'

		) ;
		
		/*
		*
		*	AddFile ( The file name ) ;
		*
		*/
		
		public static function AddFile ( $FileName )
		
		{

			$FileName = strtolower ( $FileName ) ;
		
			self::$File = fopen ( BASEPATH.$FileName , self::$_Method ['ADD'] ) ;
			
			if ( self::$File )
			
			{
			
				return self::$File ;
			
			}
			
			self::CloseFile () ;
			
		}
		
		/*
		*
		*	WriteFile ( The file name , Text ) ;
		*
		*/
		
		public static function WriteFile ( $FileName , $Text )
		
		{
		
			$FileName = strtolower ( $FileName ) ;
			
			self::$File = fopen ( BASEPATH.$FileName , self::$_Method ['WRITE'] ) ;
			
			if ( self::$File )
			
			{
			
				if ( strlen ( $Text ) > 0 )
				
				{
				
					$Write = fwrite ( self::$File , $Text ) ;
					
					return $Write ;
				
				}
			
			}
		
			self::CloseFile () ;
		
		}
		
		/*
		*
		*	ReadFile ( The file name , Number of characters that will have on offer starting from zero or ALL ) ;
		*
		*/
		
		public static function ReadFile ( $FileName , $FileSize )

		{

			$FileName = strtolower ( $FileName ) ;
			
			self::$File = fopen ( BASEPATH.$FileName , self::$_Method ['READ'] ) ;
			
			if ( self::$File )
			
			{
				
				if ( is_numeric ( $FileSize ) )
				
				{
				
					$Size = $FileSize ;
				
				}
				
				else if ( $FileSize == 'ALL' )

				{
				
					$Size = filesize ( $FileName ) ;
				
				}
				
				if ( $Size > 0 )
				
				{

					$Read = fread ( self::$File , $Size ) ;
					
					return $Read ;
					
				}

			}
			
			self::CloseFile () ;

		}
		
		/*
		*
		*	EditFile ( The file name , Text ) ;
		*
		*/
		
		public static function EditFile ( $FileName , $Text , $Replace )
		
		{
		
			$FileName = strtolower ( $FileName ) ;
			
			self::$File = self::ReadFile ( $FileName , 'ALL' ) ;
			
			if ( strlen ( self::$File ) > 0 )
			
			{
			
				if ( ( strlen ( $Text ) > 0 ) && ( strlen ( $Replace ) > 0 ) )
				
				{
				
					$EditFile = str_replace ( $Text , $Replace , self::$File ) ;
					
					$EditFile = self::WriteFile ( $FileName , $EditFile ) ;
					
					return $EditFile ;
				
				}

			}
			
			unset ( $EditFile ) ;

		}
		
		/*
		*
		*	CloseFile () ;
		*
		*/
		
		private static function CloseFile ()
		
		{
		
			fclose ( self::$File ) ;

		}
		
		/*
		*
		*	RemoveFile ( The file name ) ;
		*
		*/
		
		public function RemoveFile ( $FileName )
		
		{
		
			$FileName = strtolower ( $FileName ) ;
			
			if ( self::FileExs ( $FileName ) )
			
			{
			
				unlink ( BASEPATH.$FileName ) ;
				
				return ;
			
			}
		
		}
		
		/*
		*
		*	FileExs ( The file name ) ;
		*
		*/
		
		public static function FileExs ( $FileName )
		
		{
		
			$FileName = strtolower ( $FileName ) ;
		
			return ( boolean ) file_exists ( BASEPATH.$FileName ) ? TRUE : FALSE ;

		}
	
	}
	
	class Load
	
	{
	
		/*
		*
		*	Files variable.
		*
		*/
	
		public $Files ;
		
		/*
		*
		*	Run DealingFiles class.
		*
		*/
	
		public function __construct ()
		
		{
		
			$this->Files = new DealingFiles () ;
			
			return $this->Files ;
		
		}
	
	}

?>