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

		int dirsSearched = 0, filesFound = 0;

		List<Task> tasks = new List<Task>();
		Dictionary<string, DateTime> lastAccessed = new Dictionary<string, DateTime>();

		// Check if all the directories exist
		static public bool DoesAllDirectoriesExist(List<string> directories)
		{
			if (directories.Count > 0)
			{
				foreach (string dir in directories)
				{
					if (!Directory.Exists(dir))
					{
						if (dir != "") Console.WriteLine(dir + " does not exist");
						else Console.WriteLine("Directory does not exist");
						return false;
					}
				}
			}
			else
			{
				Console.WriteLine("Directory does not exist!");
				return false;
			}

			return true;
		}

		public void Init(List<string> directories)
		{
			Task.Run(() =>
			{
				while (true) // loop forever
				{
					if (directories.Count > 0)
					{
						foreach (string dir in directories)
						{
							if (Directory.Exists(dir))
							{
								SearchDirectory(dir);
								//Console.WriteLine("Searched " + dirsSearched + " folders and found " + filesFound + " files.");
							}
						}
					}
				}
			});
		}

		public void WaitEnd()
		{
			Task.WaitAll(tasks.ToArray());
		}

		void SearchDirectory(string path)
		{
			List<string> directories, files;

			// append '\' to the path if it isn't already there
			if (path.Last() != '\\') path += '\\';
			//Console.WriteLine("Searching through " + path);

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
					DateTime fileLastAccessed;
					if (!lastAccessed.ContainsKey(file) ||
						(lastAccessed.TryGetValue(file, out fileLastAccessed) && File.GetLastAccessTimeUtc(file) > fileLastAccessed))
					{
						lastAccessed.Add(file, DateTime.UtcNow);
						Indexer.Instance.AddFileName(file);
						tasks.Add(Task.Run(() => Parser.Instance.ParseFile(file)));
						filesFound++;
					}

				}
			}

			dirsSearched++;
		}
	}
}
