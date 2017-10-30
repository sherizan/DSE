using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SQLite;

// Open Sourced library
// Fluent Command Line Parser
// https://github.com/fclp/fluent-command-line-parser
using Fclp;

namespace Crawler
{
	class Program
	{
		static void Main(string[] args)
		{
			List<string> directories = new List<string>();
			bool doReplaceDB = false;

			if (args.Length > 0)
			{
				// parse arguments
				var p = new FluentCommandLineParser();
				p.Setup<List<string>>('d', "directories").Callback(items => directories = items)
					.WithDescription("Directories to crawl.");
				p.Setup<string>('s', "savepath").Callback(s => Indexer.dbDirectory = s)
					.WithDescription("Path to save the database file.");
				p.Setup<bool>('r', "replace").Callback(b => doReplaceDB = b)
					.WithDescription("Replace existing database file");
				p.SetupHelp("h", "help").Callback(text => { Console.WriteLine(text); Environment.Exit(1); });
				p.HelpOption.ShowHelp(p.Options);
				p.Parse(args);
			}
			else
			{
				Console.Write("Enter directories to search, seperated by spaces: ");
				string input = "";
				while (input == "") input = Console.ReadLine(); // wait for input
				string[] split = input.Split(' ');
				for (int i = 0; i < split.Length; ++i)
				{
					if (split[i][0] != '\"') // substring has double quotes
					{
						string dir = split[i];
						while (++i < split.Length && split[i].Last() != '\"')
						{
							dir += ' ' + split[i];
						}

						directories.Add(dir);
					}
					else
					{
						directories.Add(split[i]);
					}
				}
			}

			Console.CursorVisible = false;
			DateTime start = DateTime.Now;

			if (Crawler.Instance.Search(directories))
			{
				Indexer.Instance.Init(doReplaceDB);
				Indexer.Instance.WaitToEnd();

				Console.WriteLine("Total time taken: " + (DateTime.Now - start).TotalMilliseconds + "ms");
			}

			Console.CursorVisible = true;
			if (args.Length == 0) Console.Read();
		}
	}
}
