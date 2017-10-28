using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Text.RegularExpressions;

namespace Crawler
{
	public class Parser
	{
		private Parser() { }
		static Parser() { }
		private static Parser _instance = new Parser();
		public static Parser Instance { get { return _instance; } }

		Regex rgx = new Regex("[^a-zA-Z0-9 -]");

		//static string[] ValidFileTypes = { ".txt", ".html", ".c", ".cpp", ".cs", ".js", ".java" };
		static char[] whitespaceCharacters = { ' ', '\f', '\n', '\r', '\t', '\v'};

		public void ParseFile(string path)
		{
			int oldCursorY, newCursorY;
			lock (Console.Out)
			{
				Console.WriteLine("\tParsing " + path.Substring(path.LastIndexOf("\\")) + "... ");
				oldCursorY = Console.CursorTop - 1;
			}
			string allText;
			string[] split = { };
			List<string> cleaned = new List<string>();

			try
			{
				allText = File.ReadAllText(path);
				split = allText.Split(whitespaceCharacters, StringSplitOptions.RemoveEmptyEntries);

				foreach (string s in split)
				{
					string[] split2 = rgx.Replace(s, " ").Split(whitespaceCharacters, StringSplitOptions.RemoveEmptyEntries);
					foreach (string s2 in split2) if (s2 != null || s2 != "") cleaned.Add(s2.ToLower());
				}
			}
			catch (Exception e)
			{
				Console.WriteLine(e.Message);
			}

			for (int i = 0; i < cleaned.Count; ++i)
			{
				string s = cleaned[i];
				Indexer.Instance.AddWordEntry(s);
				Indexer.Instance.AddWordLink(s, path, i < cleaned.Count - 1 ? cleaned[i + 1] : "");
			}

			lock (Console.Out)
			{
				newCursorY = Console.CursorTop;
				Console.CursorLeft = 100; Console.CursorTop = oldCursorY;
				Console.SetCursorPosition(100, oldCursorY);
				Console.Write("Done!");
				Console.CursorLeft = 0; Console.CursorTop = newCursorY;
				Console.SetCursorPosition(0, newCursorY);
			}
		}
	}
}
