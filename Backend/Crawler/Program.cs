using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SQLite;

namespace Crawler
{
	class Program
	{
		static void Main(string[] args)
		{
			string directory = "";
			if (args.Length > 0) directory = args[0];
			else
			{
				Console.Write("Enter a directory to search: ");
				directory = Console.ReadLine();
			}

			Indexer.Instance.Init();
			Crawler.Instance.SetRootDirectory(directory);
			Crawler.Instance.Start();

			Console.ReadLine();
		}
	}
}
