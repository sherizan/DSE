using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Crawler
{
	class Crawler
	{
		private Crawler() { }
		static Crawler() { }
		private static Crawler _instance = new Crawler();
		public static Crawler Instance { get { return _instance; } }

		string rootDirectory = "";
		int dirsSearched = 0, filesFound = 0;

		public void SetRootDirectory(string directory)
		{
			rootDirectory = directory;
			if (rootDirectory.Last() != '\\') rootDirectory += '\\';
		}

		public void Start()
		{
			SearchDirectory(rootDirectory);

			Console.WriteLine("Searched " + dirsSearched + " folders and found " + filesFound + " files.");
		}

		void SearchDirectory(string path)
		{
			List<string> directories, files;

			// append '\' to the path if it isn't already there
			if (path.Last() != '\\') path += '\\';
			Console.WriteLine("Searching through " + path);

			try
			{
				directories = Directory.EnumerateDirectories(path).ToList();
				files = Directory.EnumerateFiles(path).ToList();
			}
			catch
			{
				return;
			}

			if (directories.Count > 0)
			{
				foreach (string dir in directories)
				{
					SearchDirectory(dir);
				}
			}

			if (files.Count > 0)
			{
				foreach (string file in files)
				{
					// filename is extracted (w/o path)
					Indexer.Instance.AddFileName(path, file.Substring(file.LastIndexOf("\\") + 1));
					filesFound++;
				}
			}

			dirsSearched++;
		}
	}
}
