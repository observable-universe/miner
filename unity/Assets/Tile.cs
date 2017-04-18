using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using ObservableUniverse.Generators;
using ObservableUniverse.Noise;

public class Tile : MonoBehaviour {

	public OpenSimplexNoise noise;
	public int iterator = 1;

	public Vector3[] vertices;
  public int[] triangles;
	public Vector2[] uvs;

	public const int columns = 240;
	public const int vertColumns = columns + 1;

	public const int rows = 240;
	public const int vertRows = rows + 1;

	public const int area = vertColumns * vertRows;
	private const int haltIndex = area - 1;

  void Start() {
		noise = new OpenSimplexNoise(5);
		vertices = new Vector3[area];

		uvs = new Vector2[area];

		int vertexIndex = 0;
		for(int rowIndex = 0; rowIndex < vertRows; rowIndex++) {
			for(int columnIndex = 0; columnIndex < vertColumns; columnIndex++) {
				int x = columnIndex;
				int y = rowIndex;

				// Assign x, y and z for each vertex
				Vector3 vector = new Vector3{x=x, y=y, z=(float)noise.Evaluate(x,y)};
				vertices[vertexIndex] = vector;

				// @todo Add a comment explaining this
				float vectorX = vector.x == 0 ? vector.x : vector.x / vertRows;
				float vectorY = vector.y == 0 ? vector.y : vector.y / vertColumns;
				uvs[vertexIndex] = new Vector2(vectorX, vectorY);

				// Increment vertex index
				vertexIndex++;
			}
		}

		TriangleGenerator triangleGenerator = new TriangleGenerator();
		triangles = triangleGenerator.run(vertices, columns, rows);

		Mesh mesh = GetComponent<MeshFilter>().mesh;
    mesh.vertices = vertices;
		mesh.triangles = triangles;
		mesh.uv = uvs;
		mesh.RecalculateNormals();
  }

	// Update is called once per frame
	void Update () {

	}
}
